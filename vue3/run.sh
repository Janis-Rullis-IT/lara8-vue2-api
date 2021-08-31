#!/bin/sh

set -e
npm install
npm run serve
service nginx start && tail -F /var/log/nginx/error.log