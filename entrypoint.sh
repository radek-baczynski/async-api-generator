#!/bin/sh -l

echo "Hello, I'm generating docs for: ${1} to output directory: ${2}"

node /asyncapi/cli -t /generator/templates -o "${2}" "${1}" html

echo "::set-output name=docsDir::${2}"
