# Fundraising Nightly Currency Conversion Tool

Currency conversion tool for donations from multiple currencies into USD.

## Purpose

Use currency conversion rates that are updated automatically on a daily basis from a 3rd party service that provides us with that day’s current conversion rates for a limited number of supported currencies.  The service is a simple API that outputs XML when called with the URL:

https://wikitech.wikimedia.org/wiki/Fundraising/tech/Currency_conversion_sample?ctype=text/xml&action=raw

This XML sample is a small static subset of supported currencies. For the purposes of this task, you should assume that we support many more currencies, and that the exchange rates for those currencies are expected to change on a once-daily basis.

Define a MySQL table that can store the daily currency conversion data.

## Goal

Accomplish the following tasks:

* Retrieving the data from the API (assuming this will be triggered by a cron job)
* Parse the data
* Store the data in a MySQL table
* Given an amount of a foreign currency, convert it into the equivalent in US dollars. For example:
input: 'JPY 10000'
output: 'USD 131.25'
* Given an array of amounts in foreign currencies, return an array of US equivalent amounts in the same order. For example:
input: array( 'JPY 10000', 'CZK 10000' )
output: array( 'USD 131.25', 'USD 519' )


## Authors & Acknowledgments

* Review, updates, adds, testing by me (Steve Demo).

Many thanks to Josh Lawton!
* Project: CurrencyConverter https://github.com/joshlawton/Currency-Converter/
* Author: https://github.com/joshlawton/ (2011)
