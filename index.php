<?php
include('config/config.php');
if ((!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) ||
    ($_SERVER['PHP_AUTH_USER'] != $CONFIG['username'] || $_SERVER['PHP_AUTH_PW'] !== $CONFIG['password'])) {
    header("WWW-Authenticate: Basic realm=\"Thermobox\"");
    header("HTTP/1.0 401 Unauthorized");
    print "Ask Sebastian for the password";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Thermobox</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#322821">
    <meta http-equiv=X-UA-Compatible content="IE=edge">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0e0e0e;
            font-family: Arial, sans-serif;
            color: #f0f0f0;
        }

        a {
            color: #f0f0f0;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 3fr;
            grid-template-rows: 1fr;
        }

        .container div {
            padding: 20px;
            overflow: auto;
            min-width: 320px;
            max-height: calc(100vh - 40px);
        }

        .sidebar ul {
            list-style-type: none;
            margin: 10px 0;
            padding-left: 20px;
        }

        .sidebar > ul {
            padding: 0;
        }

        .sidebar a {
            padding: 4px 0;
        }

        .content * {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <ul class="tree-root"></ul>
    </div>
    <div class="content">
    </div>
</div>

<script>
    fetch('api/artifact/')
        .then(r => r.json())
        .then(buildTree);

    const root = document.getElementsByClassName('tree-root')[0];
    const content = document.getElementsByClassName('content')[0];

    function buildTree(tree) {

        Object.keys(tree).forEach(date => {
            let dateNode = document.createElement('li');
            let dateList = document.createElement('ul');
            dateNode.innerText = date;
            root.appendChild(dateNode);
            dateNode.appendChild(dateList);

            Object.keys(tree[date]).forEach(time => {
                let timeNode = document.createElement('li');
                let timeList = document.createElement('ul');
                timeNode.innerText = time;
                dateList.appendChild(timeNode);
                timeNode.appendChild(timeList);

                tree[date][time].forEach(artifact => {
                    let artifactNode = document.createElement('li');
                    let link = document.createElement('a');
                    link.href = "";
                    link.innerText = artifact.name;
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        showContent(artifact.filename);
                    });
                    artifactNode.appendChild(link);
                    timeList.appendChild(artifactNode);
                });
            });
        });
    }

    function showContent(filename) {
        while (content.firstChild) {
            content.removeChild(content.firstChild);
        }

        if (filename.endsWith('.txt') || filename.endsWith('.json')) {
            fetch(filename)
                .then(response => response.text())
                .then(text => {
                    const pre = document.createElement('pre');
                    pre.innerText = text;
                    content.appendChild(pre);
                });
        } else if (filename.endsWith('.jpg')) {
            const img = document.createElement('img');
            img.src = filename;
            content.appendChild(img);
        } else if (filename.endsWith('.mp4')) {
            const video = document.createElement('video');
            video.src = filename;
            video.autoplay = true;
            video.controls = true;
            content.appendChild(video);
        }
    }
</script>
</body>
</html>
