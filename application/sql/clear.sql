DELETE FROM ip_products;

DELETE FROM ip_invoice_items;
DELETE FROM ip_invoice_item_amounts;
DELETE FROM ip_invoice_amounts;
DELETE FROM ip_invoices;
DELETE FROM ip_invoice_groups;
DELETE FROM ip_invoice_tax_rates;

DELETE FROM ip_quote_items;
DELETE FROM ip_quote_item_amounts;
DELETE FROM ip_quotes;
DELETE FROM ip_quote_amounts;
DELETE FROM ip_quote_tax_rates;

DELETE FROM ip_tasks_time;
DELETE FROM ip_tasks_alert;
DELETE FROM ip_tasks_assignment;
DELETE FROM ip_tasks;

DELETE FROM ip_suppliers;
DELETE FROM ip_companies;

DELETE FROM ip_clients;
DELETE FROM ip_client_notes;

DELETE FROM ip_payments;

DELETE FROM ip_stock;
DELETE FROM ip_stock_alert;
DELETE FROM ip_stock_history;

DELETE FROM ip_users WHERE user_email != 'info@finans.lt';

