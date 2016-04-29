<?php

	$api_key = 'API_KEY';
	$api_secret = 'API_SECRET';
	$access_token = 'ACCESS_TOKEN';
	$access_token_secret = 'ACCESS_TOKEN_SECRET';

	$tweet = new tweet_bot;
	$tweet->setKey($api_key, $api_secret, $access_token, $access_token_secret);
	
	# GET my most recent tweet
	$latest_tweet = $tweet->latestTweet();

	# Print the timestamp from most recent tweet
	$timestamp = strtotime($latest_tweet[0]->created_at);
	print('<pre>Latest Tweet: ' . date('r', $timestamp) . '</pre>');

	# Determine whether an hour has passed since last tweet
	if( ($timestamp + 3600) <= time() ) {
		
		# GET 100 most recent tweets w/ HASHTAG
		$results = $tweet->search('HASHTAG');
		$favorite_count = 0;
		$favorite_id = 0;

		# Loop through results to find the most favorited tweet
		foreach ($results->statuses as $status) {
			if($status->favorite_count > $favorite_count) {
				$favorite_count = $status->favorite_count;
				$favorite_id = $status->id;
			}
		}

		# Retweet the most favorited tweet
		$retweet = $tweet->retweet($favorite_id);

	}

	# =======================================

class tweet_bot {
    function oauth() {
      require('twitteroauth/autoload.php');
      $connection = new Abraham\TwitterOAuth\TwitterOAuth($this->api_key, $this->api_secret, $this->access_token, $this->access_token_secret);
      return $connection;
    }
    function latestTweet() {
      $connection = $this->oauth();
      $status = $connection->get('statuses/user_timeline', array('screen_name' => 'SCREEN_NAME', 'count' => 1));
      return $status;
    }
    function tweet($text) {
      $connection = $this->oauth();
      $status = $connection->post('statuses/update', array('status' => $text));
      return $status;
    }
    function retweet($id) {
      $connection = $this->oauth();
      $status = $connection->post('statuses/retweet/' . $id);
      return $status;
    }
    function search($text) {
    	$connection = $this->oauth();
    	$statuses = $connection->get('search/tweets', array(
    		'q' => $text . ' -filter:replies AND -filter:retweets', 
    		'lang' => 'en', 
    		'result_type' => 'recent', 
    		'count' => 100
    	));
    	return $statuses;
    }
    function setKey($api_key, $api_secret, $access_token, $access_token_secret) {
      $this->api_key = $api_key;
      $this->api_secret = $api_secret;
      $this->access_token = $access_token;
      $this->access_token_secret = $access_token_secret;
    }
}
