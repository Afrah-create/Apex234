#!/usr/bin/env python3
"""
Test script for the Machine Learning Demand Forecasting Module
"""

import json
import sys
import os
from new_demand_forecasting import AdvancedDemandForecaster

def test_ml_module():
    """Test the ML module with various scenarios"""
    
    print("🧪 Testing Machine Learning Demand Forecasting Module")
    print("=" * 60)
    
    # Test 1: Basic functionality
    print("\n1️⃣ Testing basic forecast generation...")
    try:
        forecaster = AdvancedDemandForecaster()
        result = forecaster.generate_forecast(3)
        
        if result.get('status') == 'success':
            print("✅ Basic forecast generation: PASSED")
            print(f"   - Historical records: {len(result.get('historical', []))}")
            print(f"   - Predicted months: {len(result.get('predicted', []))}")
            print(f"   - Best model: {result.get('best_model')}")
            print(f"   - Confidence level: {result.get('confidence_level'):.2f}")
        else:
            print("❌ Basic forecast generation: FAILED")
            print(f"   - Error: {result.get('error', 'Unknown error')}")
            return False
            
    except Exception as e:
        print(f"❌ Basic forecast generation: FAILED - {e}")
        return False
    
    # Test 2: Different forecast periods
    print("\n2️⃣ Testing different forecast periods...")
    try:
        periods = [1, 6, 12]
        for period in periods:
            result = forecaster.generate_forecast(period)
            if result.get('status') == 'success' and len(result.get('predicted', [])) == period:
                print(f"✅ {period}-month forecast: PASSED")
            else:
                print(f"❌ {period}-month forecast: FAILED")
                return False
    except Exception as e:
        print(f"❌ Different forecast periods: FAILED - {e}")
        return False
    
    # Test 3: Data quality checks
    print("\n3️⃣ Testing data quality...")
    try:
        df = forecaster.load_and_preprocess_data()
        if df is not None and len(df) > 0:
            print("✅ Data loading: PASSED")
            print(f"   - Records loaded: {len(df)}")
            print(f"   - Date range: {df['Date'].min()} to {df['Date'].max()}")
            print(f"   - Products: {df['product_name'].nunique()}")
            print(f"   - Regions: {df['Region'].nunique()}")
        else:
            print("❌ Data loading: FAILED")
            return False
    except Exception as e:
        print(f"❌ Data quality check: FAILED - {e}")
        return False
    
    # Test 4: Model performance
    print("\n4️⃣ Testing model performance...")
    try:
        X, y, features = forecaster.prepare_features(df)
        model_scores = forecaster.train_models(X, y, features)
        
        if model_scores and len(model_scores) > 0:
            print("✅ Model training: PASSED")
            for model_name, scores in model_scores.items():
                print(f"   - {model_name}: R²={scores['r2_score']:.3f}, MAE={scores['mae']:.1f}")
        else:
            print("❌ Model training: FAILED")
            return False
    except Exception as e:
        print(f"❌ Model performance: FAILED - {e}")
        return False
    
    # Test 5: API integration
    print("\n5️⃣ Testing API integration...")
    try:
        from new_demand_forecast_api import run_advanced_demand_forecast
        api_result = run_advanced_demand_forecast(3)
        
        if api_result.get('status') == 'success':
            print("✅ API integration: PASSED")
        else:
            print("❌ API integration: FAILED")
            print(f"   - Error: {api_result.get('error', 'Unknown error')}")
            return False
    except Exception as e:
        print(f"❌ API integration: FAILED - {e}")
        return False
    
    print("\n" + "=" * 60)
    print("🎉 All tests PASSED! Machine Learning module is working correctly.")
    print("=" * 60)
    
    return True

def generate_sample_output():
    """Generate a sample output for documentation"""
    print("\n📊 Sample Forecast Output:")
    print("-" * 40)
    
    try:
        forecaster = AdvancedDemandForecaster()
        result = forecaster.generate_forecast(3)
        
        # Print a formatted sample
        sample = {
            "summary": {
                "status": result.get('status'),
                "best_model": result.get('best_model'),
                "confidence": result.get('confidence_level'),
                "trend": result.get('trend_direction'),
                "trend_strength": result.get('trend_strength')
            },
            "predictions": result.get('predicted', []),
            "model_accuracy": result.get('model_accuracy', {}),
            "data_summary": result.get('data_summary', {})
        }
        
        print(json.dumps(sample, indent=2))
        
    except Exception as e:
        print(f"Error generating sample: {e}")

if __name__ == "__main__":
    if len(sys.argv) > 1 and sys.argv[1] == "--sample":
        generate_sample_output()
    else:
        success = test_ml_module()
        if success:
            print("\n🚀 Machine Learning module is ready for production use!")
        else:
            print("\n⚠️  Some tests failed. Please check the issues above.")
            sys.exit(1) 