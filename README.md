# Vendor_CustomOrderProcessing Module

## Description

This Magento 2 module enhances the order processing workflow by:

- Allowing external systems to update order status via a secure REST API.
- Logging order status changes to a custom database table.
- Sending email notifications when an order is marked as 'shipped'.

## Installation

1. Extract files into `app/code/Vendor/CustomOrderProcessing/`.
2. Run:

```
bin/magento setup:upgrade
bin/magento cache:flush
```

3. Verify the module is enabled:

```
bin/magento module:status Vendor_CustomOrderProcessing
```

## API Usage

### Endpoint

```
POST /rest/V1/custom-order/update-status
```

### JSON Payload

```json
{
  "orderIncrementId": "000000001",
  "newStatus": "processing"
}
```

### Authentication

Use Magento token-based authentication (e.g., Bearer Token).

## Architectural Notes

- Follows PSR-4 autoloading and Magento best practices.
- Uses dependency injection for all services.
- Uses `db_schema.xml` for Magento 2.4.7 compatibility.
- Avoids ObjectManager and direct SQL.
