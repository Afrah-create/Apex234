#!/usr/bin/env python3
"""
Demand Forecasting API for Laravel Integration
This script uses the existing demand forecasting model and returns JSON output
"""

import pandas as pd
import numpy as np
import json
import sys
import os
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error
from datetime import datetime, timedelta

def run_demand_forecast(months=3):
    """
    Run demand forecasting and return JSON result
    """
    try:
        # Load your CSV file
        csv_path = os.path.join(os.path.dirname(__file__), 'caramel_yoghurt2.csv')
        df = pd.read_csv(csv_path)
        
        # Convert 'Date' to datetime
        df['Date'] = pd.to_datetime(df['Date'], dayfirst=True, errors='coerce')
        
        # Feature engineering: extract date parts
        df['Year'] = df['Date'].dt.year
        df['Month'] = df['Date'].dt.month
        df['Day'] = df['Date'].dt.day
        df['DayOfWeek'] = df['Date'].dt.dayofweek
        
        # Select features for modeling
        features = [
            'Year', 'Month', 'Day', 'DayOfWeek',
            'Yoghurt Brand', 'SKU', 'Price', 'Stock levels', 'Order quantities'
        ]
        target = 'Number of products sold'
        
        # Encode categorical features
        df_model = df[features + [target]].copy()
        df_model = pd.get_dummies(df_model, columns=['Yoghurt Brand', 'SKU'])
        
        # Split data
        X = df_model.drop('Number of products sold', axis=1)
        y = df_model['Number of products sold']
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        # Train model
        model = RandomForestRegressor(n_estimators=100, random_state=42)
        model.fit(X_train, y_train)
        
        # Predict and evaluate
        y_pred = model.predict(X_test)
        mae = mean_absolute_error(y_test, y_pred)
        rmse = np.sqrt(mean_squared_error(y_test, y_pred))
        
        # Generate future predictions
        last_date = df['Date'].max()
        future_dates = [last_date + timedelta(days=30*i) for i in range(1, months + 1)]
        
        future_predictions = []
        for date in future_dates:
            # Create feature vector for future date
            future_features = {
                'Year': date.year,
                'Month': date.month,
                'Day': date.day,
                'DayOfWeek': date.weekday(),
                'Price': df['Price'].mean(),
                'Stock levels': df['Stock levels'].mean(),
                'Order quantities': df['Order quantities'].mean()
            }
            
            # Add dummy variables for categorical features
            for col in X.columns:
                if col not in future_features:
                    if 'Yoghurt Brand' in col or 'SKU' in col:
                        future_features[col] = 0
                    else:
                        future_features[col] = 0
            
            # Create feature vector
            feature_vector = []
            for col in X.columns:
                feature_vector.append(future_features.get(col, 0))
            
            # Predict
            prediction = model.predict([feature_vector])[0]
            future_predictions.append(max(0, int(prediction)))
        
        # Calculate seasonal patterns
        monthly_avg = df.groupby(df['Date'].dt.month)['Number of products sold'].mean().to_dict()
        seasonal_patterns = {str(k): float(v) for k, v in monthly_avg.items()}
        
        # Analyze trend
        recent_avg = df[df['Date'] >= df['Date'].max() - timedelta(days=90)]['Number of products sold'].mean()
        older_avg = df[df['Date'] < df['Date'].max() - timedelta(days=90)]['Number of products sold'].mean()
        
        if recent_avg > older_avg * 1.1:
            trend_direction = 'increasing'
        elif recent_avg < older_avg * 0.9:
            trend_direction = 'decreasing'
        else:
            trend_direction = 'stable'
        
        # Feature importance
        importances = model.feature_importances_
        feature_names = X.columns
        feature_importance = {str(feature_names[i]): float(importances[i]) for i in range(len(feature_names))}
        
        # Prepare result
        result = {
            'forecast': future_predictions,
            'confidence_level': max(0.5, 1 - (rmse / y.mean())),
            'seasonal_patterns': seasonal_patterns,
            'trend_direction': trend_direction,
            'model_accuracy': {
                'mae': float(mae),
                'rmse': float(rmse),
                'r2_score': float(model.score(X_test, y_test))
            },
            'feature_importance': feature_importance,
            'status': 'success'
        }
        
        return result
        
    except Exception as e:
        error_result = {
            'error': str(e),
            'forecast': [1000] * months,
            'confidence_level': 0.7,
            'seasonal_patterns': {},
            'trend_direction': 'stable',
            'status': 'error'
        }
        return error_result

if __name__ == "__main__":
    # Get months from command line argument, default to 3
    months = int(sys.argv[1]) if len(sys.argv) > 1 else 3
    
    # Run forecast and print JSON result
    result = run_demand_forecast(months)
    print(json.dumps(result)) 