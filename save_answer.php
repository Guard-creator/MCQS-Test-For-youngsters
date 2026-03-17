<?php
        session_start();

        if (!isset($_POST['answer'])) {
            die("No answer selected.");
        }

        $answer = $_POST['answer'];
        $index = $_SESSION['test_index'];
        $mcqs = $_SESSION['test_mcqs'];

        $mcq_id = $mcqs[$index];

        $_SESSION['test_answers'][$mcq_id] = $answer;

        $_SESSION['test_index']++;

        header("Location: take_question.php");
        exit;
?>
