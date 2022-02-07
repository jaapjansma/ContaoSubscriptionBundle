# Subscription Bundle for Contao

This module contains the following:

* **Subscriptions** which have a start and expire date and a maximum number of users (members).
* **Invoices** which are linked to subscriptions. 
* **Subscription renewal** as soon as an invoice is paid and the subscription is expired the subscription will be renewed with one year
* **Subscription expire** as soon as the expire date of the subscription is reached then an invoice is created and the subscription is set to inactive
* **Members** could be linked to a subscription 

## Customisations

You can customize the invoice by copying the `src\Resources\contao\templates\subscription_invoice.html5` to your `templates` directory and alter the file.

## Installation

`composer require edeveloper/contao-subscription-bundle`

## License

AGPL 3.0
