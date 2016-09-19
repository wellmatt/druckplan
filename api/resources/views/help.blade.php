<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>AppLL API</title>
    <style type="text/css">
        body {
            font-family: Trebuchet MS, sans-serif;
            font-size: 15px;
            color: #444;
            margin-right: 24px;
        }

        h1	{
            font-size: 25px;
        }
        h2	{
            font-size: 20px;
        }
        h3	{
            font-size: 16px;
            font-weight: bold;
        }
        hr	{
            height: 1px;
            border: 0;
            color: #ddd;
            background-color: #ddd;
            display: none;
        }

        .app-desc {
            clear: both;
            margin-left: 20px;
        }
        .param-name {
            width: 100%;
        }
        .license-info {
            margin-left: 20px;
        }

        .license-url {
            margin-left: 20px;
        }

        .model {
            margin: 0 0 0px 20px;
        }

        .method {
            margin-left: 20px;
        }

        .method-notes	{
            margin: 10px 0 20px 0;
            font-size: 90%;
            color: #555;
        }

        pre {
            padding: 10px;
            margin-bottom: 2px;
        }

        .http-method {
            text-transform: uppercase;
        }

        pre.get {
            background-color: #0f6ab4;
        }

        pre.post {
            background-color: #10a54a;
        }

        pre.put {
            background-color: #c5862b;
        }

        pre.delete {
            background-color: #a41e22;
        }

        .huge	{
            color: #fff;
        }

        pre.example {
            background-color: #f3f3f3;
            padding: 10px;
            border: 1px solid #ddd;
        }

        code {
            white-space: pre;
        }

        .nickname {
            font-weight: bold;
        }

        .method-path {
            font-size: 1.5em;
            background-color: #0f6ab4;
        }

        .up {
            float:right;
        }

        .parameter {
            width: 500px;
        }

        .param {
            width: 500px;
            padding: 10px 0 0 20px;
            font-weight: bold;
        }

        .param-desc {
            width: 700px;
            padding: 0 0 0 20px;
            color: #777;
        }

        .param-type {
            font-style: italic;
        }

        .param-enum-header {
            width: 700px;
            padding: 0 0 0 60px;
            color: #777;
            font-weight: bold;
        }

        .param-enum {
            width: 700px;
            padding: 0 0 0 80px;
            color: #777;
            font-style: italic;
        }

        .field-label {
            padding: 0;
            margin: 0;
            clear: both;
        }

        .field-items	{
            padding: 0 0 15px 0;
            margin-bottom: 15px;
        }

        .return-type {
            clear: both;
            padding-bottom: 10px;
        }

        .param-header {
            font-weight: bold;
        }

        .method-tags {
            text-align: right;
        }

        .method-tag {
            background: none repeat scroll 0% 0% #24A600;
            border-radius: 3px;
            padding: 2px 10px;
            margin: 2px;
            color: #FFF;
            display: inline-block;
            text-decoration: none;
        }
    </style>
</head>
<body>
<h1>AppLL API</h1>
<div class="app-desc"></div>
<div class="app-desc">More information: <a href="http://www.limburg-live.com">http://www.limburg-live.com</a></div>
<div class="app-desc">Contact Info: <a href="mailto: mail@kleindruck.de">mail@kleindruck.de</a></div>
<div class="app-desc">Author: Alexander Scherer</div>
<div class="app-desc">Version: 1.0.2</div>
<div class="app-desc">Updated: 2016-02-23</div>
<div class="license-info">All rights reserved</div>
<h2>Access</h2>


<h2><a name="__Methods">Methods</a></h2>
[ Jump to <a href="#__Models">Models</a> ]


<h2>Table of Contents </h2>
<div class="method-summary"></div>

<ol>



    <li><a href="#apiV1NewsGet"><code><span class="http-method">get</span> /api/v1/news</code></a></li>

    <li><a href="#apiV1NewsPost"><code><span class="http-method">post</span> /api/v1/news</code></a></li>

    <li><a href="#apiV1NewsPut"><code><span class="http-method">put</span> /api/v1/news/{id}</code></a></li>

    <li><a href="#apiV1NewsPhotosPost"><code><span class="http-method">post</span> /api/v1/news_photos</code></a></li>

    <li><a href="#oauthAccessTokenPost"><code><span class="http-method">post</span> /oauth/access_token</code></a></li>

    <li><a href="#apiV1dispatch"><code><span class="http-method">get</span> /api/v1/dispatch</code></a></li>

    <li><a href="#apiV1PushMsgsPost"><code><span class="http-method">post</span> /api/v1/push_msgs</code></a></li>



</ol>






<div class="method"><a name="apiV1NewsGet"/>
    <div class="method-path">
        <a class="up" href="#__Methods">Up</a>
        <pre class="get"><code class="huge"><span class="http-method">get</span> /api/v1/news</code></pre></div>
    <div class="method-summary">All news (<span class="nickname">NewsGet</span>)</div>

    <div class="method-notes">The news endpoint returns all existing news</div>







    <h3 class="field-label">Query parameters</h3>
    <div class="field-items">
        <div class="param">Authorization (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Header Parameter</span> &mdash; the access token<br />
                example: <br />
                Authorization:Bearer Ve40A8KkCTUYuGlj0ossXjhOhJWtgzptPHJdJGzG</p>
        </div>
    </div>  <!-- field-items -->







    <h3 class="field-label">Return type</h3>
    <div class="return-type">
        array[&quot;data&quot;][object[<a href="#News">News</a>]]</div>


    <!--Todo: process Response Object and its headers, schema, examples -->




    <h3 class="field-label">Produces</h3>
    This API call produces the following media types according to the <span class="header">Accept</span> request header;
    the media type will be conveyed by the <span class="heaader">Content-Type</span> response header.
    <ul>

        <li><code>application/json</code></li>

    </ul>


    <h3 class="field-label">Responses</h3>

    <h4 class="field-label">200</h4>
    An array of News<br />
    <em>example:</em><br />
    {
    "data": [
    {
    "id": 1,
    "title": "dfgdfgdfg",
    "heading": "dfgdfgd",
    "body": "fgdfgdfgdfg",
    "crtdate": {
    "date": "2016-02-08 17:09:22",
    "timezone_type": 3,
    "timezone": "UTC"
    },
    "appapproved": 1
    },
    {
    "id": 12,
    "title": "test 1",
    "heading": "test 1",
    "body": "test 1",
    "crtdate": {
    "date": "2016-02-08 17:09:22",
    "timezone_type": 3,
    "timezone": "UTC"
    },
    "appapproved": 1
    }
    ],
    "meta": {
    "available_includes": [],
    "default_includes": []
    }
    }
    <h4 class="field-label">400</h4>
    No valid token received


    <h4 class="field-label">500</h4>
    Unexpected error


</div> <!-- method -->
<hr/>

<div class="method"><a name="apiV1NewsPost"/>
    <div class="method-path">
        <a class="up" href="#__Methods">Up</a>
        <pre class="post"><code class="huge"><span class="http-method">post</span> /api/v1/news</code></pre></div>
    <div class="method-summary">Insert new news (<span class="nickname">NewsPost</span>)</div>

    <div class="method-notes">This endpoint is used to insert new news into the database</div>










    <h3 class="field-label">Query parameters</h3>
    <div class="field-items">
        <div class="param">data (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Query Parameter</span> &mdash; json data array news <br />
                <em>example:</em><br />
                &quot;data&quot;:<br />
                {<br />
                &quot;title&quot;: &quot;test 1&quot;,<br />
                &quot;heading&quot;: &quot;test 1&quot;,<br />
                &quot;body&quot;: &quot;test 1&quot;,<br />
                &quot;category&quot;: &quot;Sport&quot;,<br />
                &quot;crtdate&quot;: &quot;1454951362&quot;,<br />
                &quot;appapproved&quot;: 1<br />
                }<br />
                <br />
            </p>
        </div>
        <div class="field-items">
            <div class="param">Authorization (required)</div>

            <div class="param-desc">
                <p><span class="param-type">Header Parameter</span> &mdash; the access token<br />
                    <em>example: </em><br />
                    Authorization:Bearer Ve40A8KkCTUYuGlj0ossXjhOhJWtgzptPHJdJGzG</p>
            </div>
        </div>  <!-- field-items -->
    </div>  <!-- field-items -->





    <h3 class="field-label">Return type</h3>
    <div class="return-type">
        array[&quot;data&quot;][Object[<a href="#News">News]</a>]

    </div>


    <!--Todo: process Response Object and its headers, schema, examples -->




    <h3 class="field-label">Produces</h3>
    This API call produces the following media types according to the <span class="header">Accept</span> request header;
    the media type will be conveyed by the <span class="heaader">Content-Type</span> response header.
    <ul>

        <li><code>application/json</code></li>

    </ul>


    <h3 class="field-label">Responses</h3>

    <h4 class="field-label">200</h4>
    the newly created news object
    <em><br />
        example:</em><br />
    "data":
    {
    "title": "test 1",
    "heading": "test 1",
    "body": "test 1",
    "category": "Sport",
    "crtdate": "1454951362",
    "appapproved": 1
    }


    <h4 class="field-label">400</h4>
    No valid token received


    <h4 class="field-label">0</h4>
    Unexpected error


</div> <!-- method -->
<hr/>

<div class="method"><a name="apiV1NewsPut"/>
    <div class="method-path">
        <a class="up" href="#__Methods">Up</a>
        <pre class="put"><code class="huge"><span class="http-method">put</span> /api/v1/news/{id}</code></pre></div>
    <div class="method-summary">Update a news (<span class="nickname">NewsPost</span>)</div>

    <div class="method-notes">This endpoint is used to update a news record</div>










    <h3 class="field-label">Query parameters</h3>
    <div class="field-items">
        <div class="param">data (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Query Parameter</span> &mdash; json data array news <br />
                <em>example:</em><br />
                &quot;data&quot;:<br />
                {<br />
                &quot;title&quot;: &quot;test 1&quot;,<br />
                &quot;heading&quot;: &quot;test 1&quot;,<br />
                &quot;body&quot;: &quot;test 1&quot;,<br />
                &quot;category&quot;: &quot;Sport&quot;,<br />
                &quot;crtdate&quot;: &quot;1454951362&quot;,<br />
                &quot;appapproved&quot;: 1<br />
                }<br />
                <br />
            </p>
        </div>
        <div class="field-items">
            <div class="param">Authorization (required)</div>

            <div class="param-desc">
                <p><span class="param-type">Header Parameter</span> &mdash; the access token<br />
                    <em>example: </em><br />
                    Authorization:Bearer Ve40A8KkCTUYuGlj0ossXjhOhJWtgzptPHJdJGzG</p>
            </div>
        </div>  <!-- field-items -->
    </div>  <!-- field-items -->





    <h3 class="field-label">Return type</h3>
    <div class="return-type">
        array[&quot;data&quot;][Object[<a href="#News">News]</a>]

    </div>


    <!--Todo: process Response Object and its headers, schema, examples -->




    <h3 class="field-label">Produces</h3>
    This API call produces the following media types according to the <span class="header">Accept</span> request header;
    the media type will be conveyed by the <span class="heaader">Content-Type</span> response header.
    <ul>

        <li><code>application/json</code></li>

    </ul>


    <h3 class="field-label">Responses</h3>

    <h4 class="field-label">200</h4>
    the updated news object
    <em><br />
        example:</em><br />
    "data":
    {
    "title": "test 1",
    "heading": "test 1",
    "body": "test 1",
    "category": "Sport",
    "crtdate": "1454951362",
    "appapproved": 1
    }


    <h4 class="field-label">400</h4>
    No valid token received


    <h4 class="field-label">0</h4>
    Unexpected error


</div> <!-- method -->
<hr/>

<div class="method"><a name="apiV1NewsPhotosPost"/>
    <div class="method-path">
        <a class="up" href="#__Methods">Up</a>
        <pre class="post"><code class="huge"><span class="http-method">post</span> /api/v1/news_photos</code></pre></div>
    <div class="method-summary">Insert new news-photo (<span class="nickname">apiV1NewsPhotosPost</span>)</div>

    <div class="method-notes">This endpoint is used to insert new news-photos into the database</div>










    <h3 class="field-label">Query parameters</h3>
    <div class="field-items">
        <div class="param">data (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Query Parameter</span> &mdash; json data array news <br />
                <em>example:</em><br />
                &quot;data&quot;:<br />
                {<br />
                &quot;news&quot;:1,<br />
                &quot;url&quot;:&quot;https://www.codetutorial.io/wordpress/wp-content/uploads/2015/02/headerlaravel2.jpg&quot;,<br />
                &quot;crtdate&quot;:0<br />
                }</p>
        </div>
        <div class="param">Authorization (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Header Parameter</span> &mdash; the access token<br />
                <em>example: </em><br />
                Authorization:Bearer Ve40A8KkCTUYuGlj0ossXjhOhJWtgzptPHJdJGzG</p>
        </div>
    </div>  <!-- field-items -->





    <h3 class="field-label">Return type</h3>
    <div class="return-type">
        array[<a href="#NewsPhoto">NewsPhoto</a>]

    </div>


    <!--Todo: process Response Object and its headers, schema, examples -->




    <h3 class="field-label">Produces</h3>
    This API call produces the following media types according to the <span class="header">Accept</span> request header;
    the media type will be conveyed by the <span class="heaader">Content-Type</span> response header.
    <ul>

        <li><code>application/json</code></li>

    </ul>


    <h3 class="field-label">Responses</h3>

    <h4 class="field-label">200</h4>
    the newly created news-photo object<br />
    example:<br />
    "data": {
    "id": 1,
    "news": "1",
    "url": "94931bd3947b0d596b54144b784bca7f.jpg",
    "crtdate": {
    "date": "2016-02-08 17:09:22",
    "timezone_type": 3,
    "timezone": "UTC"
    }
    },
    "meta": {
    "available_includes": [],
    "default_includes": []
    }
    <h4 class="field-label">400</h4>
    No valid token received


    <h4 class="field-label">0</h4>
    Unexpected error


</div> <!-- method -->
<hr/>

<div class="method"><a name="oauthAccessTokenPost"/>
    <div class="method-path">
        <a class="up" href="#__Methods">Up</a>
        <pre class="post"><code class="huge"><span class="http-method">post</span> /oauth/access_token</code></pre></div>
    <div class="method-summary">Authentication (<span class="nickname">oauthAccessTokenPost</span>)</div>

    <div class="method-notes">Grab the authentication token.</div>






    <h3 class="field-label">Query parameters</h3>
    <div class="field-items">
        <div class="param">grant_type (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Query Parameter</span> &mdash; grant_type <br />
                <em>example:</em><br />
                grant_type: client_credentials</p>
        </div>
        <div class="param">client_id (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Query Parameter</span> &mdash; client_id <br />
                <em>example:</em><br />
                client_id: IU4fmn83L3m</p>
        </div>
        <div class="param">client_secret (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Query Parameter</span> &mdash; client_secret <br />
                <em>example:</em><br />
                client_secret: /3&amp;10fM4-LKn!</p>
        </div>
    </div>
    <!-- field-items -->







    <h3 class="field-label">Return type</h3>
    <div class="return-type">
        array[<a href="#Token">Token</a>]

    </div>


    <!--Todo: process Response Object and its headers, schema, examples -->




    <h3 class="field-label">Produces</h3>
    This API call produces the following media types according to the <span class="header">Accept</span> request header;
    the media type will be conveyed by the <span class="heaader">Content-Type</span> response header.
    <ul>

        <li><code>application/json</code></li>

    </ul>


    <h3 class="field-label">Responses</h3>

    <h4 class="field-label">200</h4>
    your auth token<br />
    <em>example:</em><br />
    {
    "access_token": "Ve40A8KkCTUYuGlj0ossXjhOhJWtgzptPHJdJGzG",
    "token_type": "Bearer",
    "expires_in": 3600
    }


</div> <!-- method -->
<hr/>



<div class="method"><a name="apiV1dispatch"/>
    <div class="method-path">
        <a class="up" href="#__Methods">Up</a>
        <pre class="get"><code class="huge"><span class="http-method">get</span> /api/v1/dispatch</code></pre></div>
    <div class="method-summary">Dispatch (<span class="nickname">Dispatch</span>)</div>

    <div class="method-notes">Dispatch Job to update App news stream.</div>






    <h3 class="field-label">Query parameters</h3>
    <div class="field-items">
        <div class="param">Authorization (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Header Parameter</span> &mdash; the access token<br />
                <em>example: </em><br />
                Authorization:Bearer Ve40A8KkCTUYuGlj0ossXjhOhJWtgzptPHJdJGzG</p>
        </div>
    </div>  <!-- field-items -->




    <h3 class="field-label">Responses</h3>

    <h4 class="field-label">200</h4>
    Dispatched!<br />


</div> <!-- method -->
<hr/>

<div class="method"><a name="apiV1PushMsgsPost"/>
    <div class="method-path">
        <a class="up" href="#__Methods">Up</a>
        <pre class="post"><code class="huge"><span class="http-method">post</span> /api/v1/push_msgs</code></pre></div>
    <div class="method-summary">Send new push message (<span class="nickname">apiV1PushMsgsPost</span>)</div>

    <div class="method-notes">This endpoint is used to store and immediately send a new push message</div>










    <h3 class="field-label">Query parameters</h3>
    <div class="field-items">
        <div class="param">data (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Query Parameter</span> &mdash; json data array news <br />
                <em>example:</em><br />
                &quot;data&quot;:<br />
                {<br />
                &quot;target&quot;:1,<br />
                &quot;message&quot;:&quot;this is a test message&quot;,<br />
                }</p>
        </div>
        <div class="param">Authorization (required)</div>

        <div class="param-desc">
            <p><span class="param-type">Header Parameter</span> &mdash; the access token<br />
                <em>example: </em><br />
                Authorization:Bearer Ve40A8KkCTUYuGlj0ossXjhOhJWtgzptPHJdJGzG</p>
        </div>
    </div>  <!-- field-items -->





    <h3 class="field-label">Return type</h3>
    <div class="return-type">
        array[<a href="#NewsPhoto">PushMsg</a>]

    </div>


    <!--Todo: process Response Object and its headers, schema, examples -->




    <h3 class="field-label">Produces</h3>
    This API call produces the following media types according to the <span class="header">Accept</span> request header;
    the media type will be conveyed by the <span class="heaader">Content-Type</span> response header.
    <ul>

        <li><code>application/json</code></li>

    </ul>


    <h3 class="field-label">Responses</h3>

    <h4 class="field-label">200</h4>
    the newly created push-msg object<br />
    example:<br />
    {"data":{"id":3,"target":"1","message":"Dies ist ein test","response":"","crtdate":{"date":"2016-02-23 14:27:12","timezone_type":3,"timezone":"UTC"}},"meta":{"available_includes":[],"default_includes":[]}}
    <h4 class="field-label">400</h4>
    No valid token received


    <h4 class="field-label">0</h4>
    Unexpected error


</div> <!-- method -->
<hr/>



<div class="up"><a href="#__Models">Up</a></div>
<h2><a name="__Models">Models</a></h2>
[ Jump to <a href="#__Methods">Methods</a> ]

<h2>Table of Contents</h2>
<ol>


    <li><a href="#Token"><code>Token</code></a></li>



    <li><a href="#News"><code>News</code></a></li>



    <li><a href="#NewsPhoto"><code>NewsPhoto</code></a></li>



    <li><a href="#PushMsg"><code>PushMsg</code></a></li>



    <li><a href="#Error"><code>Error</code></a></li>



    <li><a href="#No-Token"><code>No-Token</code></a></li>


</ol>



<div class="model">
    <h3 class="field-label"><a name="Token">Token</a> <a class="up" href="#__Models">Up</a></h3>
    <div class="field-items">
        <div class="param">access_token </div><div class="param-desc"><span class="param-type">String</span> your access token</div>

        <div class="param">token_type </div><div class="param-desc"><span class="param-type">String</span> the token type</div>

        <div class="param">expires_in </div><div class="param-desc"><span class="param-type">Integer</span> token time in seconds</div>


    </div>  <!-- field-items -->
</div>



<div class="model">
    <h3 class="field-label"><a name="News">News</a> <a class="up" href="#__Models">Up</a></h3>
    <div class="field-items">
        <div class="param">id </div><div class="param-desc"><span class="param-type">Integer</span> </div>

        <div class="param">title </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">heading </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">body </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">category </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">crtdate </div><div class="param-desc"><span class="param-type">Carbon</span> </div>

        <div class="param">appapproved </div><div class="param-desc"><span class="param-type">String</span> </div>


    </div>  <!-- field-items -->
</div>



<div class="model">
    <h3 class="field-label"><a name="NewsPhoto">NewsPhoto</a> <a class="up" href="#__Models">Up</a></h3>
    <div class="field-items">
        <div class="param">id </div><div class="param-desc"><span class="param-type">Integer</span> </div>

        <div class="param">news </div><div class="param-desc"><span class="param-type">Integer</span> </div>

        <div class="param">url </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">crtdate </div><div class="param-desc"><span class="param-type">Carbon</span> </div>


    </div>  <!-- field-items -->
</div>



<div class="model">
    <h3 class="field-label"><a name="PushMsg">PushMsg</a> <a class="up" href="#__Models">Up</a></h3>
    <div class="field-items">
        <div class="param">id </div><div class="param-desc"><span class="param-type">Integer</span> </div>

        <div class="param">target </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">message </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">response </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">crtdate </div><div class="param-desc"><span class="param-type">Carbon</span> </div>


    </div>  <!-- field-items -->
</div>



<div class="model">
    <h3 class="field-label"><a name="Error">Error</a> <a class="up" href="#__Models">Up</a></h3>
    <div class="field-items">
        <div class="param">code </div><div class="param-desc"><span class="param-type">Integer</span> </div>

        <div class="param">message </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">fields </div><div class="param-desc"><span class="param-type">String</span> </div>


    </div>  <!-- field-items -->
</div>



<div class="model">
    <h3 class="field-label"><a name="No-Token">No-Token</a> <a class="up" href="#__Models">Up</a></h3>
    <div class="field-items">
        <div class="param">error </div><div class="param-desc"><span class="param-type">String</span> </div>

        <div class="param">error_description </div><div class="param-desc"><span class="param-type">String</span> </div>


    </div>  <!-- field-items -->
</div>


</body>
</html>
