#!/usr/bin/env python3
"""
New Demand Forecasting Module for Laravel Integration
"""

import pandas as pd
import numpy as np
import json
import sys
import os
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor, GradientBoostingRegressor
from sklearn.linear_model import LinearRegression
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sklearn.preprocessing import LabelEncoder, StandardScaler
from datetime import datetime, timedelta
import warnings
warnings.filterwarnings('ignore')

class AdvancedDemandForecaster:
    def __init__(self, csv_path=None, suppress_output=False):
        if csv_path is None:
            # Always use the absolute path relative to this script's location
            self.csv_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'demanForecasting.csv')
        else:
            self.csv_path = csv_path
        self.models = {}
        self.label_encoders = {}
        self.scaler = StandardScaler()
        self.feature_importance = {}
        self.suppress_output = suppress_output
        
    def load_and_preprocess_data(self):
        import os
        if not self.suppress_output:
            print('Looking for CSV at:', os.path.abspath(self.csv_path))
        try:
            # Load the CSV file
            df = pd.read_csv(self.csv_path)
            if not self.suppress_output:
                print(f"Loaded {len(df)} records from {self.csv_path}")
            
            # Convert Date to datetime
            df['Date'] = pd.to_datetime(df['Date'], format='%d/%m/%Y', errors='coerce')
            
            # Extract date features
            df['Year'] = df['Date'].dt.year
            df['Month'] = df['Date'].dt.month
            df['Day'] = df['Date'].dt.day
            df['DayOfWeek'] = df['Date'].dt.dayofweek
            df['Quarter'] = df['Date'].dt.quarter
            df['WeekOfYear'] = df['Date'].dt.isocalendar().week
            
            # Create seasonal features
            df['IsWeekend'] = df['DayOfWeek'].isin([5, 6]).astype(int)
            df['IsMonthEnd'] = df['Date'].dt.is_month_end.astype(int)
            df['IsMonthStart'] = df['Date'].dt.is_month_start.astype(int)
            
            # Handle missing values
            df['Inventory Level'] = df['Inventory Level'].fillna(df['Inventory Level'].median())
            df['Units Sold'] = df['Units Sold'].fillna(0)
            df['Units Ordered'] = df['Units Ordered'].fillna(0)
            df['unit_price(UGX)'] = df['unit_price(UGX)'].fillna(df['unit_price(UGX)'].median())
            
            # Create derived features
            df['Inventory_Turnover'] = df['Units Sold'] / (df['Inventory Level'] + 1)
            df['Order_Fulfillment_Rate'] = df['Units Sold'] / (df['Units Ordered'] + 1)
            df['Revenue_Per_Unit'] = df['total_price(UGX)'] / (df['Units Sold'] + 1)
            
            # Encode categorical variables
            categorical_features = ['retailer_name', 'retailer_id', 'Product_id', 'product_name', 
                                  'Region', 'Weather Condition', 'order_status']
            
            for feature in categorical_features:
                if feature in df.columns:
                    le = LabelEncoder()
                    df[f'{feature}_encoded'] = le.fit_transform(df[feature].astype(str))
                    self.label_encoders[feature] = le
            
            # Create product category feature
            df['Product_Category'] = df['product_name'].str.extract(r'(\w+)').fillna('Other')
            le_category = LabelEncoder()
            df['Product_Category_encoded'] = le_category.fit_transform(df['Product_Category'])
            self.label_encoders['Product_Category'] = le_category

            # --- AGGREGATE TO MONTHLY DATA ---
            df['YearMonth'] = df['Date'].dt.to_period('M')
            
            # Aggregate by month only (total monthly demand across all products and regions)
            monthly_df = df.groupby(['YearMonth']).agg({
                'Units Sold': 'sum',  # Total monthly demand
                'Inventory Level': 'sum',  # Total inventory
                'Units Ordered': 'sum',  # Total orders
                'unit_price(UGX)': 'mean',  # Average price
                'Inventory_Turnover': 'mean',  # Average turnover
                'Order_Fulfillment_Rate': 'mean',  # Average fulfillment rate
                'Revenue_Per_Unit': 'mean',  # Average revenue per unit
                'Promotion': 'max',  # Any promotion in the month
                'retailer_id_encoded': 'first',  # Use first retailer as representative
                'Product_id_encoded': 'first',  # Use first product as representative
                'product_name_encoded': 'first',  # Use first product name as representative
                'Region_encoded': 'first',  # Use first region as representative
                'Weather Condition_encoded': 'first',  # Use first weather as representative
                'order_status_encoded': 'first',  # Use first status as representative
                'Product_Category_encoded': 'first'  # Use first category as representative
            }).reset_index()
            
            # Convert YearMonth back to datetime
            monthly_df['Date'] = monthly_df['YearMonth'].dt.to_timestamp()
            
            # Add back date features for the aggregated data
            monthly_df['Year'] = monthly_df['Date'].dt.year
            monthly_df['Month'] = monthly_df['Date'].dt.month
            monthly_df['Day'] = 1
            monthly_df['DayOfWeek'] = monthly_df['Date'].dt.dayofweek
            monthly_df['Quarter'] = monthly_df['Date'].dt.quarter
            monthly_df['WeekOfYear'] = monthly_df['Date'].dt.isocalendar().week
            monthly_df['IsWeekend'] = 0
            monthly_df['IsMonthEnd'] = monthly_df['Date'].dt.is_month_end.astype(int)
            monthly_df['IsMonthStart'] = 1
            
            if not self.suppress_output:
                print(f"Aggregated to {len(monthly_df)} monthly records")
            return monthly_df
        except Exception as e:
            if not self.suppress_output:
                print(f"Error loading data: {e}")
            return None
    
    def prepare_features(self, df):
        """Prepare features for modeling"""
        # Select features for modeling
        feature_columns = [
            'Year', 'Month', 'Day', 'DayOfWeek', 'Quarter', 'WeekOfYear',
            'IsWeekend', 'IsMonthEnd', 'IsMonthStart',
            'Inventory Level', 'Units Ordered', 'unit_price(UGX)',
            'Inventory_Turnover', 'Order_Fulfillment_Rate', 'Revenue_Per_Unit',
            'retailer_id_encoded', 'Product_id_encoded', 'product_name_encoded',
            'Region_encoded', 'Weather Condition_encoded', 'order_status_encoded',
            'Product_Category_encoded', 'Promotion'
        ]
        
        # Ensure all features exist
        available_features = [col for col in feature_columns if col in df.columns]
        
        X = df[available_features].copy()
        y = df['Units Sold']
        
        # Handle infinite values
        X = X.replace([np.inf, -np.inf], np.nan)
        X = X.fillna(X.median())
        
        return X, y, available_features
    
    def train_models(self, X, y, features):
        """Train multiple models for ensemble prediction"""
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42, shuffle=False
        )
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        # Train different models
        models = {
            'random_forest': RandomForestRegressor(n_estimators=100, random_state=42),
            'gradient_boosting': GradientBoostingRegressor(n_estimators=100, random_state=42),
            'linear_regression': LinearRegression()
        }
        model_scores = {}
        for name, model in models.items():
            try:
                if name == 'linear_regression':
                    model.fit(X_train_scaled, y_train)
                    y_pred = model.predict(X_test_scaled)
                else:
                    model.fit(X_train, y_train)
                    y_pred = model.predict(X_test)
                # Calculate metrics
                mae = mean_absolute_error(y_test, y_pred)
                rmse = np.sqrt(mean_squared_error(y_test, y_pred))
                r2 = r2_score(y_test, y_pred)
                model_scores[name] = {
                    'mae': mae,
                    'rmse': rmse,
                    'r2_score': r2
                }
                self.models[name] = model
                # Store feature importance for tree-based models
                if hasattr(model, 'feature_importances_'):
                    self.feature_importance[name] = dict(zip(features, model.feature_importances_))
                # Use only ASCII in print statements
                if not self.suppress_output:
                    print(f"{name}: MAE={mae:.2f}, RMSE={rmse:.2f}, R2={r2:.3f}")
            except Exception as e:
                if not self.suppress_output:
                    print(f"Error training {name}: {e}")
        return model_scores
    
    def generate_forecast(self, months=3):
        """Generate demand forecast for future months using monthly aggregated data and output historical actuals for visualization"""
        try:
            # Load and preprocess data (now monthly)
            df = self.load_and_preprocess_data()
            if df is None:
                return self.get_error_response(months)
            # Prepare features
            X, y, features = self.prepare_features(df)
            # Train models
            model_scores = self.train_models(X, y, features)
            if not self.models:
                return self.get_error_response(months)
            # Use the best model for forecasting
            best_model_name = max(model_scores.keys(), 
                                key=lambda x: model_scores[x]['r2_score'])
            best_model = self.models[best_model_name]
            # Generate future months
            last_date = df['Date'].max()
            future_dates = [last_date + pd.DateOffset(months=i) for i in range(1, months + 1)]
            # Prepare future feature data
            future_predictions = []
            for date in future_dates:
                future_features = self.create_future_feature_vector(date, df, features)
                if best_model_name == 'linear_regression':
                    future_features_scaled = self.scaler.transform([future_features])
                    prediction = best_model.predict(future_features_scaled)[0]
                else:
                    prediction = best_model.predict([future_features])[0]
                future_predictions.append(max(0, int(prediction)))
            # Calculate seasonal patterns (now monthly)
            seasonal_patterns = self.calculate_seasonal_patterns(df)
            # Analyze trends
            trend_analysis = self.analyze_trends(df)
            # Calculate confidence level
            confidence_level = self.calculate_confidence_level(model_scores[best_model_name])
            # --- HISTORICAL ACTUALS (aggregated by month) ---
            historical = []
            # Use the same monthly aggregation as training data
            for _, row in df.sort_values('Date').iterrows():
                historical.append({
                    'month': row['Date'].strftime('%Y-%m'),
                    'actual_sales': int(row['Units Sold'])
                })
            # --- PREDICTED (overall) ---
            predicted = []
            for i, date in enumerate(future_dates):
                predicted.append({
                    'month': date.strftime('%Y-%m'),
                    'predicted_sales': int(future_predictions[i])
                })
            # Prepare result
            result = {
                'historical': historical,
                'predicted': predicted,
                'confidence_level': confidence_level,
                'seasonal_patterns': seasonal_patterns,
                'trend_direction': trend_analysis['direction'],
                'trend_strength': trend_analysis['strength'],
                'model_accuracy': model_scores[best_model_name],
                'best_model': best_model_name,
                'feature_importance': self.feature_importance.get(best_model_name, {}),
                'data_summary': {
                    'total_records': len(df),
                    'date_range': {
                        'start': df['Date'].min().to_pydatetime().strftime('%Y-%m-%d'),
                        'end': df['Date'].max().to_pydatetime().strftime('%Y-%m-%d')
                    },
                    'months_count': len(df),
                    'total_units_sold': int(df['Units Sold'].sum())
                },
                'status': 'success',
                'forecast_months': [d.strftime('%Y-%m') for d in future_dates]
            }
            return result
        except Exception as e:
            if not self.suppress_output:
                print(f"Error in forecast generation: {e}")
            return self.get_error_response(months)
    
    def create_future_feature_vector(self, date, df, features):
        """Create feature vector for future date prediction with seasonal patterns"""
        # Base date features
        future_features = {
            'Year': date.year,
            'Month': date.month,
            'Day': date.day,
            'DayOfWeek': date.weekday(),
            'Quarter': date.quarter,
            'WeekOfYear': date.isocalendar()[1],
            'IsWeekend': 1 if date.weekday() in [5, 6] else 0,
            'IsMonthEnd': 1 if date.day == (date.replace(day=1) + timedelta(days=32)).replace(day=1) - timedelta(days=1) else 0,
            'IsMonthStart': 1 if date.day == 1 else 0
        }
        
        # Use seasonal patterns for better predictions
        month_data = df[df['Month'] == date.month]
        quarter_data = df[df['Quarter'] == date.quarter]
        
        # Use seasonal averages when available, fallback to overall medians
        if len(month_data) > 0:
            future_features.update({
                'Inventory Level': month_data['Inventory Level'].median(),
                'Units Ordered': month_data['Units Ordered'].median(),
                'unit_price(UGX)': month_data['unit_price(UGX)'].median(),
                'Inventory_Turnover': month_data['Inventory_Turnover'].median(),
                'Order_Fulfillment_Rate': month_data['Order_Fulfillment_Rate'].median(),
                'Revenue_Per_Unit': month_data['Revenue_Per_Unit'].median(),
            })
        else:
            future_features.update({
                'Inventory Level': df['Inventory Level'].median(),
                'Units Ordered': df['Units Ordered'].median(),
                'unit_price(UGX)': df['unit_price(UGX)'].median(),
                'Inventory_Turnover': df['Inventory_Turnover'].median(),
                'Order_Fulfillment_Rate': df['Order_Fulfillment_Rate'].median(),
                'Revenue_Per_Unit': df['Revenue_Per_Unit'].median(),
            })
        
        # Assume no promotion for future
        future_features['Promotion'] = 0
        
        # Add encoded features (use most common values)
        for feature in ['retailer_id', 'Product_id', 'product_name', 'Region', 'Weather Condition', 'order_status', 'Product_Category']:
            encoded_col = f'{feature}_encoded'
            if encoded_col in features:
                future_features[encoded_col] = df[encoded_col].mode().iloc[0] if not df[encoded_col].mode().empty else 0
        
        # Return feature vector in correct order
        return [future_features.get(feature, 0) for feature in features]
    
    def calculate_seasonal_patterns(self, df):
        """Calculate seasonal patterns in the data"""
        monthly_avg = df.groupby(df['Date'].dt.month)['Units Sold'].mean().to_dict()
        quarterly_avg = df.groupby(df['Date'].dt.quarter)['Units Sold'].mean().to_dict()
        
        return {
            'monthly': {str(k): float(v) for k, v in monthly_avg.items()},
            'quarterly': {str(k): float(v) for k, v in quarterly_avg.items()}
        }
    
    def analyze_trends(self, df):
        """Analyze trends in the data"""
        # Calculate moving averages
        df_sorted = df.sort_values('Date')
        recent_avg = df_sorted.tail(30)['Units Sold'].mean()
        older_avg = df_sorted.head(30)['Units Sold'].mean()
        
        # Determine trend direction and strength
        if recent_avg > older_avg * 1.1:
            direction = 'increasing'
            strength = min(1.0, (recent_avg - older_avg) / older_avg)
        elif recent_avg < older_avg * 0.9:
            direction = 'decreasing'
            strength = min(1.0, (older_avg - recent_avg) / older_avg)
        else:
            direction = 'stable'
            strength = 0.0
        
        return {
            'direction': direction,
            'strength': float(strength),
            'recent_avg': float(recent_avg),
            'older_avg': float(older_avg)
        }
    
    def calculate_confidence_level(self, model_metrics):
        """Calculate confidence level based on model performance"""
        # Higher R² score means higher confidence
        r2 = model_metrics['r2_score']
        confidence = max(0.5, min(0.95, r2 + 0.3))  # Scale R² to confidence level
        return confidence
    
    def get_error_response(self, months):
        """Return error response with fallback data"""
        return {
            'error': 'Failed to generate forecast',
            'forecast': [100] * months,
            'confidence_level': 0.7,
            'seasonal_patterns': {},
            'trend_direction': 'stable',
            'trend_strength': 0.0,
            'model_accuracy': {'mae': 0.0, 'rmse': 0.0, 'r2_score': 0.0},
            'best_model': 'fallback',
            'feature_importance': {},
            'data_summary': {},
            'status': 'error'
        }

def main():
    """Main function to run demand forecasting"""
    try:
        # Get months from command line argument, default to 3
        months = int(sys.argv[1]) if len(sys.argv) > 1 else 3
        
        # Initialize forecaster
        forecaster = AdvancedDemandForecaster()
        
        # Generate forecast
        result = forecaster.generate_forecast(months)
        
        # Print JSON result with proper formatting
        print(json.dumps(result, indent=2, ensure_ascii=False))
        
    except Exception as e:
        error_result = {
            'error': str(e),
            'status': 'error',
            'message': 'Failed to generate forecast'
        }
        print(json.dumps(error_result, indent=2))

if __name__ == "__main__":
    main() 