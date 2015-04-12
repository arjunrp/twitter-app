#A Simple Twitter Application

A ver simple twitter application to
   - Sign-in using twitter
   - Post  a tweet to users timeline
   - Fetch user tweets and basic account information
    
###How it works
   - User login
   - Application post a status to user's timeline
   - Users can follow their followers via mail by clicking the 'Follow via mail' button
   - User gets tweets of those people in mail.

The file [tweets.php] is responsible for sending emails, this file can be added to system crontab to run it periodically.
###More about real time feeds
Twitter have an excellent real time [streaming API], it can have 3 types of usages:
   - public stream - Public data(searching via hashtag)
   - user stream - Stream of a particular user
   - site stream - Stream of multiple users

As of now, the 'site stream' API is available only upon special request. The same application can be done via the site stream API to deliver tweets more real time.
 
Thanks a lot to [PHP Twitter Library] !!!