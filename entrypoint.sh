#!/bin/sh -l

echo "Hello, I'm generating docs for: ${1} to output directory: ${2}"

node /asyncapi/cli -t /generator/templates -o /generated "${1}" "${2}"
