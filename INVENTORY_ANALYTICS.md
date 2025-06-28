# Inventory Analytics Dashboard

## Overview
The Inventory Analytics Dashboard provides real-time insights into the Caramel Yogurt inventory management system. It displays comprehensive analytics, charts, and data tables to help administrators monitor stock levels, track inventory status, and make informed decisions.

## Features

### 1. Real-time Inventory Overview
- **Bar Chart Visualization**: Displays inventory levels by product category
- **Color-coded Categories**: 
  - Green: Available stock
  - Blue: Reserved inventory
  - Red: Damaged items
  - Gray: Expired products

### 2. Inventory Summary Cards
- Total Products (SKU Count)
- Available Stock
- Reserved Inventory
- Critical Stock Alerts
- Low Stock Items
- Out of Stock Items

### 3. User Distribution Analytics
- **Doughnut Chart**: Shows user distribution by role
- **Role Breakdown**: Admin, Vendor, Retailer, Supplier
- **Percentage Calculations**: Automatic calculation of user percentages

### 4. Real-time Data Table
- Product-wise inventory breakdown
- Status indicators (Normal, Low Stock, Out of Stock)
- Last updated timestamps
- Color-coded badges for different inventory states

### 5. Auto-refresh Functionality
- Automatic data refresh every 30 seconds
- Manual refresh button with visual feedback
- Real-time updates without page reload

## Access

### Main Dashboard
- URL: `/dashboard` (for admin users)
- Contains summary cards and basic charts
- Quick navigation to detailed analytics

### Detailed Analytics Page
- URL: `/admin/inventory`
- Full-featured analytics dashboard
- Comprehensive charts and data tables
- Advanced filtering and visualization options

## API Endpoints

### Chart Data
- **GET** `/api/inventory/chart-data`
- Returns formatted data for Chart.js visualization
- Includes product names and quantity breakdowns

### Summary Statistics
- **GET** `/api/inventory/summary`
- Returns inventory summary statistics
- Includes totals and alert counts

### User Statistics
- **GET** `/api/inventory/user-statistics`
- Returns user distribution data
- Includes role breakdown and percentages

## Technical Implementation

### Frontend
- **Chart.js**: For data visualization
- **Bootstrap**: For responsive layout
- **JavaScript**: For real-time data fetching and updates
- **AJAX**: For asynchronous data loading

### Backend
- **Laravel**: PHP framework
- **Eloquent ORM**: Database queries
- **JSON API**: RESTful endpoints
- **Middleware**: Authentication and authorization

### Database
- **Inventory Table**: Stores product inventory data
- **Yogurt Products Table**: Product information
- **Users Table**: User management
- **Roles Table**: Role-based access control

## Security
- Admin-only access (AdminMiddleware)
- Authentication required
- CSRF protection
- Input validation and sanitization

## Usage Instructions

1. **Access the Dashboard**:
   - Login as an admin user
   - Navigate to the main dashboard
   - Click "Detailed Analytics" button

2. **View Real-time Data**:
   - Charts automatically update every 30 seconds
   - Use the "Refresh Data" button for immediate updates
   - Monitor stock alerts and critical inventory levels

3. **Analyze Trends**:
   - Review inventory distribution across products
   - Monitor user activity and role distribution
   - Track stock levels and identify potential issues

4. **Take Action**:
   - Use the data to make inventory decisions
   - Identify products needing restocking
   - Monitor user engagement and system usage

## Customization

### Adding New Charts
1. Create new API endpoint in `InventoryController`
2. Add chart initialization in JavaScript
3. Update the view template

### Modifying Data Sources
1. Update database queries in controller methods
2. Modify data formatting for Chart.js
3. Test API endpoints

### Styling Changes
1. Modify CSS classes in the view
2. Update Chart.js configuration
3. Adjust responsive breakpoints

## Troubleshooting

### Common Issues
1. **Charts not loading**: Check API endpoints and network connectivity
2. **Data not updating**: Verify auto-refresh settings and API responses
3. **Access denied**: Ensure user has admin role and proper authentication

### Debug Mode
- Check browser console for JavaScript errors
- Verify API responses using browser developer tools
- Review Laravel logs for backend errors

## Future Enhancements
- Export functionality for reports
- Advanced filtering and search
- Historical data tracking
- Predictive analytics
- Mobile-responsive optimizations
- Real-time notifications
- Integration with external systems 