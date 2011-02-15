jQuery(document).ready(function($) {
    $('a[rel|="like_photo"]').click(function(e) {
    	var postBack = siteurl + "/wp-admin/admin-ajax.php";
    	var imageAddr = $(this).attr("href");
    	var updateArea = $(this).prev(".currentVotes");
    	var voteLink = $(this);
    	
    	$.post(postBack, {"action": "likephoto_vote", "imgid": imageAddr }, function(data) {
    		updateArea.text("Votes: " + data);
    		voteLink.fadeOut("fast");
		});
    	
    	return false;
    });
});