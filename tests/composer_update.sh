#!/usr/bin/env bash

composer remove avisota/contao-message --no-update
composer remove avisota/contao-message-element-headline --no-update
composer update --prefer-dist --no-interaction
composer require avisota/contao-message ~3.0 --no-update
composer require avisota/contao-message-element-headline ~3.0
