<!DOCTYPE html>
<html>
<body>

<form action="search.php" method="post">
	Search Reddit: <input type="search" name="query"><br>
	Subreddits to Search: <input type="search" name="white"><br>
	Subreddits to Avoid: <input type="search" name="black"><br>
	<input type="radio" name="searchType" value="all" checked>All Posts
	<input type="radio" name="searchType" value="selfPosts">Only Self Posts
	<input type="radio" name="searchType" value="images">Only Image Posts
	<input type="radio" name="searchType" value="articles">Only Article Posts<br>
	<input type="checkbox" name="nsfwFilter" value="true">Filter nsfw<br>
	<!--<input type="checkbox" name="subscribed" value="true">Search ONLY Subscribed Subreddits<br>
	Username: <input type="search" name="uname">
	Password: <input type="password" name="password"><br>-->
	<input type="submit">
</form>

</body>
</html>