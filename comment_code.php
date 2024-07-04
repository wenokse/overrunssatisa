<?php
session_start();
include('includes/dbcon.php');

if(isset($_POST['add_subreplies']))
{
    $cmt_id = mysqli_real_escape_string($con, $_POST['cmt_id']);
    $reply_msg = mysqli_real_escape_string($con, $_POST['reply_msg']);
    $user_id = $_SESSION['user'];

    $query = "INSERT INTO comment_replies (user_id,comment_id,reply_msg) VALUES ('$user_id','$cmt_id','$reply_msg')";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        echo "Comment Replied to User";
    }
    else
    {
        echo "Something Went Wrong!";
    }

}

if(isset($_POST['view_comment_data']))
{
    $cmt_id = mysqli_real_escape_string($con, $_POST['cmt_id']);

    $query = "SELECT * FROM comment_replies WHERE comment_id='$cmt_id' "; 
    $query_run = mysqli_query($con, $query);

    $result_array = [];

    if(mysqli_num_rows($query_run) > 0)
    {
        foreach($query_run as $row)
        {
            $user_id = $row['user_id'];
            $user_query = "SELECT * FROM users WHERE id='$user_id' LIMIT 1"; 
            $user_query_run = mysqli_query($con, $user_query);
            $user_result = mysqli_fetch_array($user_query_run);


            array_push($result_array, ['cmt'=>$row, 'user'=>$user_result]);
        }
        header('Content-type: application/json');
        echo json_encode($result_array);
    }
    else
    {
        echo "No replied to this user";
    }

}

if(isset($_POST['add_reply']))
{
    $cmt_id = mysqli_real_escape_string($con, $_POST['comment_id']);
    $reply = mysqli_real_escape_string($con, $_POST['reply_msg']);

    $user_id = $_SESSION['user'];

    $query = "INSERT INTO comment_replies (user_id,comment_id,reply_msg) VALUES ('$user_id','$cmt_id','$reply')";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        echo "Comment Replied";
    }
    else
    {
        echo "Something Went Wrong!";
    }
}

if(isset($_POST['comment_load_data']))
{
    $comments_query = "SELECT * FROM comments ORDER BY id DESC"; 
    $comments_query_run = mysqli_query($con, $comments_query);

    $array_result = [];

    if(mysqli_num_rows($comments_query_run) > 0)
    {
        foreach($comments_query_run as $row)
        {
            $user_id = $row['user_id'];
            $user_query = "SELECT * FROM users WHERE id='$user_id' LIMIT 1"; 
            $user_query_run = mysqli_query($con, $user_query);
            $user_result = mysqli_fetch_array($user_query_run);

            array_push($array_result, ['cmt'=>$row, 'user'=>$user_result]);
        }
        header('Content-type: application/json');
        echo json_encode($array_result);
    }
    else
    {
        echo "Give a comment";
    }
}

if(isset($_POST['add_comnment']))
{
    $msg = mysqli_real_escape_string($con, $_POST['msg']);
    $user_id = $_SESSION['user'];

    $comment_add_query = "INSERT INTO comments (user_id,msg) VALUES ('$user_id','$msg')";
    $comment_add_query_run = mysqli_query($con, $comment_add_query);

    if($comment_add_query_run)
    {
        echo "Comment Added Successfully";
    }
    else{
        echo "Comment not added.! Something wert wrong";
    }
}

?>