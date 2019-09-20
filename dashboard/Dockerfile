FROM radekb/php-dev:7.3

RUN apk add git openssh apache2-utils groff less python py-pip coreutils

# Install the `aws` CLI tool
RUN pip install --upgrade --user awscli && echo 'export PATH=/root/.local/bin:$PATH'>/root/.bashrc

# Install serverless
RUN yarn global add serverless
