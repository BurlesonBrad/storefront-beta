# My Attempt To Keep Storefront Synced with a WordPress insance

WordPress Plugin to Update and Sync WooCommerce Storefront to the Latest Release

It seem to me that there's a lot of updaters out there. The Tavern sips beer over talking about this hooplah all the time.  The AWP Facebook group loves talking about it. So, as I attempt to work with Storefront, my only goal is to keep a Storfront instance synced up with whatever Mike, James, and anyone else is pushing and merging.  I've found it damn near impossible to learn, do, twerk, and tweak anything in Storefront when there are file changes all the time.
Folks making child themes (or working w/ Storefront however the heck they want to) need two things:

1. An Uptade Notification
2. Something that'll take the current version of Storefront from WooTheme's Github Repo and let 'em work with it

_seems simple enough, right?_ The problem though is that all these updater options they keep hootin' and hollerin' about seem to demand that you're the owner or contributor or whatever for that particular git repo.  Well, that sucks.  If I 'fork' a copy of Storefront, *that ain't gonna do jack sh!t* the next time [James](https://github.com/jameskoster), [Nicola](https://github.com/SiR-DanieL), [Matty](https://github.com/mattyza), [Caleb](https://github.com/WPprodigy) or any other [CONTRIBUTOR](https://github.com/woothemes/storefront/graphs/contributors) decides to merge something like [THIS](https://github.com/woothemes/storefront/commit/47eeb7cf2a596255e7c53f04925b8e0774de73d4) or [THIS](https://github.com/woothemes/storefront/commit/6b136e02513e2c0347e70b78aa88d79b6d2498f5)

> _well, crap :poop:  Me and a lotta other folks need an update to get all those changes to our Storefront!_ So, I gotta go back, refork it again with the changes, then I get to make all these _updaters_ sync w/ **my** fork _(b/c technically *I'm the owner* of that fork)_? 

*Yeah, that ain't gonna work.*  We need a notification and the new files. Plain and simple.

At the moment of typing this, I have *zero* anticipation that I'll be able to make this work *myself*.  Although [Zac and Hampton have been watched relentlessly for hours and hours](https://teamtreehouse.com/library/topic:php) on end, there's seriously no way that my time will allow my learning curve to magically fastforward itself into knowing how to make all of this fully functional.

## I'm ok with that :wink:

As I ponder off to Google to learn the *how* and the *why* to make this work, creating Display Ads for a campaign, hooking and unhooking Woo's functions to make a layout work better for a store owner, or attempting to write and modify plugins....
Feel free to chuckle at my ignorance, laugh at my //notes// that I'm made in the file, and add some comments yourself :grin:

My only hope and goal is that you'll add a fork, add the *_why_*, and help get this plugin working so that folks can keep a working copy of Storefront running in their WordPress. 

# Thanks! 

after all this is up and running, [James](https://github.com/jameskoster) said to do a pull request that used ```Storefront_Beta_Tester``` as the name and [they'd consider merging it into Storefront's Master](https://github.com/woothemes/storefront/issues/338#issuecomment-191149389)
