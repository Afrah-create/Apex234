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
        
        # Check if CSV file exists
        if not os.path.exists(csv_path):
            raise FileNotFoundError(f"CSV file not found at: {csv_path}")
        
        # Initialize the advanced forecaster with the correct csv_path
        forecaster = AdvancedDemandForecaster(csv_path=csv_path, suppress_output=True)
        
        # Generate forecast
        result = forecaster.generate_forecast(months)
        
        # Ensure result has all required fields
        if result.get('status') != 'success':
            print(f"Warning: Forecast generation returned status: {result.get('status')}", file=sys.stderr)
        
        return result
        
    except FileNotFoundError as e:
        print(f"File error: {e}", file=sys.stderr)
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
    except Exception as e:
        print(f"Unexpected error: {e}", file=sys.stderr)
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
    
    # Suppress stdout to avoid debug output mixing with JSON
    import io
    import contextlib
    
    # Capture stdout to suppress debug prints
    f = io.StringIO()
    with contextlib.redirect_stdout(f):
        # Run forecast and get result
        result = run_advanced_demand_forecast(months)
    
    # Print only the JSON result
    print(json.dumps(result)) 