# Installation

## Step 1 : Extract the ZIP-file locally and inspect the contents

Let's say we have an example ZIP-file called `MessageBird_Magento2_EventStream.zip` which contains a module name Example. The first
step is to extract all files contained within the ZIP-file to your local computer. The extracted folder-structure could
look a bit like this:

```txt
MessageBird/MagentoEventStream/composer.json
...
```

## Step 2 : Uploading all files to the Magento filesystem

Now you're ready to upload all files to the Magento root-filesystem. You can use any approach that fits you, the point
is to move `MessageBird` folder into the `app/code` directory.

## Step 3 : Refresh the Magento cache

The next step is to refresh the Magento cache. This can be done through the Magento backend. Instead of refreshing the
Magento cache gently, we recommend you use the button Flush Cache to make sure all left-over files are gone.

## Step 4 : Logout from the Magento backend

This step is important. Logout from the Magento backend. This makes you lose your PHP-session. Next, login again.

**Success**!

