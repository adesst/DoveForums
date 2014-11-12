<div class="panel panel-primary">
    <div class="panel-heading">
        <p class="lead">
            {discussion_name}
        </p>
    </div>

    <div class="panel-body">
        <div class="panel panel-info">

            <div class="panel-body">
                <div class="col-md-2">
                    <div class="text-center">
                        <p class="text-info"><strong>{first_comment->user_info->username}</strong>
                        <span class="text-success small">{first_comment->user_info->group}</span></p>
                        <a href="#">
                            {first_comment->user_info->gravatar}
                        </a>
                        <p class="text-info">{first_comment->user_info->rank} <br /><small>({first_comment->user_info->user_xp}/{first_comment->user_info->max_xp} xp)</small></p>
                        <ul class="list-unstyled">
                            <li class="small"><span>0 Posts</span></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-10">
                    <p class="text-muted"><em>Posted {first_comment->comment_info->created_date} ago</em></p>
                    <p>{first_comment->comment_info->comment}</p>
                    <hr>
                    <p class="text-muted"><small><em>{first_comment->user_info->signature}</em></small></p>
                </div>
            </div>

            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            {first_comment->buttons->btn_reply}
                            {first_comment->buttons->btn_thumb_up}
                            {first_comment->buttons->btn_thumb_down}
                        </div>
                    </div>
                </div>
                <div class="row" id="comment_box">
                    <form role="form" class="form" id="comment_form">
                        <label>{label_comment}</label>
                        <textarea class="form-control" name="comment" placeholder="type something here"></textarea>
                        <input type="submit" class="btn btn-default" />
                    </form>
                </div>
            </div>
        </div>

        <!-- BEGIN {comments} -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><small><strong>{user_info->username}</strong></small></h4>
                </div>

                <div class="panel-body">
                    <div class="col-md-2">
                        <div class="text-center">
                            <p class="text-success">{user_info->group}</p>
                            <a href="#">
                                {user_info->gravatar}
                            </a>
                            <p class="text-info">{user_info->rank} <br /><small>({user_info->user_xp}/{user_info->max_xp} xp)</small></p>
                                <ul class="list-unstyled">
                                    <li class="small"><span>0 Posts</span></li>
                                </ul>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <p class="text-muted"><em>Posted {comment_info->created_date} ago</em></p>
                        <p>{comment_info->comment}</p>
                        <hr>
                        <p class="text-muted"><small><em>{user_info->signature}</em></small></p>
                    </div>
                </div>

                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <a href="#" class="btn btn-success btn-xs"><i class="fa fa-thumbs-up"></i></a>
                                <a href="#" class="btn btn-danger btn-xs"><i class="fa fa-thumbs-down"></i></a>
                                <a href="#" class="btn btn-success btn-xs"> This answered my question</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- END {comments} -->
    </div>
</div>
<script>
$("#comment_box").hide();

$("#reply").click(function(event){
    event.preventDefault();
    $("#comment_box").toggle();
});

$("#comment_form").submit(function(){
    
    $.ajax({
        url : {site_url},
        type : "POST",
        async : false,
        data : "comment=" + $("#comment_form textarea").val() + "&discussion_id=" + {first_comment->comment_info->discussion_id},
        success : function(msg){
        
        },
    });

    window.location.reload();
    return false;
});
</script>
