import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, mean_squared_error
import numpy as np

# Load your CSV file
df = pd.read_csv('caramel_yoghurt2.csv')

# Show the first 5 rows
print("First 5 rows:")
print(df.head())

# Show info about columns and data types
print("\nData info:")
print(df.info())

# Show summary statistics
print("\nSummary statistics:")
print(df.describe())

# Check for missing values
print("\nMissing values per column:")
print(df.isnull().sum())

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

print(df_model.head())

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

print(f"Mean Absolute Error: {mae:.2f}")
print(f"Root Mean Squared Error: {rmse:.2f}")

importances = model.feature_importances_
feature_names = X.columns
feature_importance = sorted(zip(feature_names, importances), key=lambda x: x[1], reverse=True)

print("\nTop 10 Feature Importances:")
for feature, importance in feature_importance[:10]:
    print(f"{feature}: {importance:.4f}")