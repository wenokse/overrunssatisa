<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    
    $conn = $pdo->open();

    try{
        $conn->beginTransaction();

        function deleteCommentAndLikes($conn, $commentId) {
            $stmt = $conn->prepare("DELETE FROM comment_likes WHERE comment_id = :id");
            $stmt->execute(['id' => $commentId]);
            $stmt = $conn->prepare("DELETE FROM comment WHERE id = :id");
            $stmt->execute(['id' => $commentId]);
        }

        function deleteRepliesRecursively($conn, $parentId) {
            $stmt = $conn->prepare("SELECT id FROM comment WHERE parent_id = :parent_id");
            $stmt->execute(['parent_id' => $parentId]);
            $replies = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($replies as $replyId) {
                deleteRepliesRecursively($conn, $replyId);
                deleteCommentAndLikes($conn, $replyId);
            }
        }
        deleteRepliesRecursively($conn, $id);
        deleteCommentAndLikes($conn, $id);
        $conn->commit();

       
        echo json_encode(array('success' => true));
    }
    catch(PDOException $e){
        $conn->rollBack();
        $_SESSION['error'] = $e->getMessage();
        echo json_encode(array('success' => false));
    }

    $pdo->close();
}
else{
    echo json_encode(array('success' => false));
}
?>