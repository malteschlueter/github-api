# Github API

## What is Github API
Github API is a symfony console application to interact with the Github API.

## Installation
```bash
$ make init
```

Add a [personal access token](https://github.com/settings/tokens/new) as a environment variable _GITHUB_PERSONAL_ACCESS_TOKEN_ and the username as _GITHUB_USERNAME_. 

## Available commands
* `github:workflow:runs` Show all workflow runs of a repository
* `github:workflow:run:delete` Delete all workflow runs of a repository
