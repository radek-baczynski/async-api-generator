FROM asyncapi/generator

COPY templates /generator/templates

COPY entrypoint.sh /run/entrypoint.sh

RUN chmod +x /run/entrypoint.sh

ENTRYPOINT ["/run/entrypoint.sh"]
