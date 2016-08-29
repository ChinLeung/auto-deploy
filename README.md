# Auto Deploy
This repository contains a small script that will allow your server to auto-pull from the git project whenever there's a new push with the help of Project Webhooks.

## Installation
Clone or download the project, then move the two files in the `src` directory to anywhere that is accessible via an address in your project.

For example:  

    http://example.com/deploy.php

Once you've moved the files, you need to add a Webhook in your Git Project:

### Github
1. Go to your project
2. Click on **Settings**
3. Click on **Webhook & Services** on the left
4. Click on **Add webhook** at the top
5. Enter your password to continue (if not asked to, proceed to step 6)
6. In the Payload URL, put the location of your deploy.php
7. In the Content type dropdown, choose **application/x-www-form-urlencoded**
8. In the events option, tick **Just the push event.**
9. Make sure the `active` checkbox is checked.
10. Click on **Add webhook**

### Gitlab
1. Go to your project
2. Click on Project Settings
3. Click on **Webhooks**
4. In the URL, put the location of your deploy.php
5. In the trigger option, tick **Push events**
6. Click on **Add Webhook**

After you've setup the webhook, it's time to configure the config file that came with the project. If you open it, you'll see a [JSON](http://www.w3schools.com/json/) like this:  

    {
      "project-name": "Auto Puller",
      "host": "example.com",
      "pull-branch": "refs/heads/master",
      "log-file": "autodeploy.log",
      "log-access": true
    }

## Options

| Option | Type | Required | Default | Description |
| :---: | :---: | :---: | :---: | --- |
| project-name | String | No | Auto Puller | The project name is the window title when someone tries to access the deploy script manually. |
| host | String | Yes | - | The domain or the ip address of the server that is sending the webhook. In other words, it's either github or your gitlab domain name / ip address. |
| pull-branch | String | No | refs/heads/master | Since the webhook will be sent every time a push event occurs in all branches, this is to tell the script to only pull when the push event is for the specified branch. |
| log-file | String | No | autodeploy.log | The path to the log file. |
| log-access | Boolean | No | true | True to keep track of every time the deploy script is called. |

## Notes
You need to make sure that the user running apache on your server has access and permissions to perform a `git pull`.
