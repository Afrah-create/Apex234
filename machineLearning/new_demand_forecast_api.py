#!/usr/bin/env python3
"""
Advanced Demand Forecasting API for Laravel Integration
This script uses the new demand forecasting module with demanForecasting.csv
"""

import sys
import os
import json
from new_demand_forecasting import AdvancedDemandForecaster

def run_advanced_demand_forecast(months=3):
    """
    Run advanced demand forecasting and return JSON result
    """
    try:
        # Find the path to the CSV file in the same directory as this script
        csv_path = os.path.join(os.path.dirname(__file__), 'demanForecasting.csv')
        # Initialize the advanced forecaster with the correct csv_path
        forecaster = AdvancedDemandForecaster(csv_path=csv_path)
        
        # Generate forecast
        result = forecaster.generate_forecast(months)
        
        return result
        
    except Exception as e:
        error_result = {
            'error': str(e),
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
        return error_result

if __name__ == "__main__":
    # Get months from command line argument, default to 3
    months = int(sys.argv[1]) if len(sys.argv) > 1 else 3
    
    # Run forecast and print JSON result
    result = run_advanced_demand_forecast(months)
    print(json.dumps(result)) 