
Technical Description:
- Laravel + Angular 
- Separate Deployment model: Backend CICD(Github, Render), Frontend CICD(Github, Netlify)
- Technical challenge: Realtime data tracking through server send event
- Main func: 
    * Live Json Server data (Bread api design + data streaming)
    * Social media trends live charts
    * WordCloud Generation perday 


Scenario: we try to simulate a JSON server
The main focus of our server is to render data about post on social media, each post add a hashtag

- First try with flat file
    + have a flat json file with data which is constantly served by a streamed response

    + accepted request (/api/)
        * GET:  /posts  => return all post in a json format
        * GET:  /streamed/posts => return stream response on post to track and send new changes
        * PUT:  /posts/update/:postid => update a post
        * GET:  /posts/wordcloud


- Process for the front app
    + home page: generate and display a new word cloud each day
        * Laravel procedure: to genereate and save in a secure folder a word cloud using wordcloud api each day
        * Laravel procedure: to delete the previuos generated cloud 
        * merge both in async way

    + result search page: display keyword or post force across social media
        * keyword: lasts months nb of appearnace in each social
        * topic: last month most similar topic and their post force for each social


- Notes: the role is to augmnt tags presence or post force
    + update what to augment : post update (hashtage,retweet,like) or new post
    + possible usage of cron task and queus: jobs(genrating and storing an image once every day), queus(uploading a csv file)
    + format of an event stream: prefix each data line with "data: " and appending a newline character ("\\n\\n") to separate events

#  =================== WORD AVANCEMENT ===================
                ⚠️ - ❌ - ⭕️ - 💯 - 🔘 - ✅

- Rest api method: JSON SERVER
    + add a post ✅
    + update a post nb og retweet like or new hastag ✅
    + get a post ✅
    + get all post ✅
    + update streaming route: ✅

- Wordcloud Scheduled Jobs
    + Generate wordcloud image daily ✅
    + store image ✅
    + store url to daily image in session ✅
    + update generate wordcloud: select and send a random text as data to post request ⚠️

- Rest api method: CLIENT APP SERVING
    + home page: get daily wordcloud image
    + Live data tracking page: get trends data for last six month and then stream any update
    + Search result page: search a topic or a hastage
        * get similar post data and organise per platform
        * get nb post associated with organise per platform

- DB initialisation from csv file
    + transfer controller method to a queu 