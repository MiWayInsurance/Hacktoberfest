# Hacktoberfest
Simple dashboard to show the progress of Hacktoberfest PRs between teams

### Setup guide
1. Clone the repository
1. Setup the web server (for example NGINX or Apache)
1. Run `composer install`
1. Run `yarn install`
1. Run `./node_modules/.bin/encore dev`
1. You will need a GitHub access token. You can read more on GitHub access tokens [here](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line/). Once you have your token, add the following entry to your `.env` file: `GITHUB_AUTH_TOKEN=<your-access-token>`
