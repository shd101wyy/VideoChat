<!DOCTYPE html>
<?php 
  if(isset($_GET["dest"])){
    $dest = $_GET["dest"];
  }
  else{
    $dest = "0";
  }
?>
<html>
    <head>
        <!-- Video Chat app using peer.js -->
        <title> Video Chat </title>
        <script src="./peer.js"></script>     
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <link rel="stylesheet" type="text/css" href="css/index.css">
    </head>
    <body>
        <video id="video" autoplay="autoplay"></video>
        <video id="other_video" autoplay="autoplay"></video>
        <div id="main_page" class="slide">
          <div id="wrapper">
            <h1 id="big_head"> Video Chat </h1>
            <p id="author">by shd101wyy</p>
            <button id="enter_our_world_btn"> Create Room </button>
          </div>
        </div>
    </body>
    <script type="text/javascript">
      $(document).ready(function(){
        var DEST = "<? echo $dest; ?>"; 
        if(DEST !== "0"){
          $("#main_page").hide();
        }
        var lOCAL_MEDIA_STREAM;
        var startVideoChat;
        $("#video").hide();
        $("#other_video").hide();
        var peer = new Peer({ key: 'yba4z07j6lzvvx6r', debug: 3, config: {'iceServers': [
                  { url: 'stun:stun.l.google.com:19302' } // Pass in optional STUN and TURN server for maximum network compatibility
                ]}});
        var MY_ID = "";
        peer.on('open', function(id){
            console.log("my id is " + id);
            MY_ID = id;
        });
        navigator.getUserMedia = ( navigator.getUserMedia ||
                             navigator.webkitGetUserMedia ||
                             navigator.mozGetUserMedia ||
                             navigator.msGetUserMedia);
        if (navigator.getUserMedia) {
           navigator.getUserMedia (
              // constraints
              {
                 video: true,
                 audio: true
              },
              // successCallback
              function(localMediaStream) {
                lOCAL_MEDIA_STREAM = localMediaStream
                 // Do something with the video here, e.g. video.play()

                peer.on('connection', function(conn){ // receive connection
                    conn.on('data', function(data){
                        console.log("received:" + data);
                        startVideoChat(data);
                    })
                }); 

                if (DEST === '0') { // no room found
                    $("#enter_our_world_btn").click(function(){
                        $("#big_head").html("Room Created!");
                        $("#author").html("<br>ask your friend to go to this link ;)<br>\n" + "<strong>http://planetwalley.com/VideoChat/index.php?dest="+MY_ID+"</strong>");
                        $("#enter_our_world_btn").hide();
                    })
                }
                else{
                      var conn = peer.connect(DEST); // start connection
                      conn.on('open', function(){
                          // send data
                          conn.send(MY_ID);
                      })
                      startVideoChat(DEST);
                }
              },
              // errorCallback
              function(err) {
                 console.log("The following error occured: " + err);
              }
           );
        } else {
           alert("getUserMedia not supported");
        } 

        startVideoChat = function(remote_user_id){
          console.log("START VIDEO CHAT WITH " + remote_user_id);
          $("#main_page").hide();
          $("#video").show();
          $("#other_video").show();

          $("#video").css("position", "absolute");
          $("#video").css("width", $(window).width());
          $("#video").css("height", $(window).height());
          $("#other_video").css("position", "absolute");
          $("#other_video").css("width", "400px");
          $("#other_video").css("height", "400px");
          $("#other_video").css("right", "0px");
          $("#other_video").css("bottom", "0px");



          var video = document.getElementById('video');
          video.src = window.URL.createObjectURL(lOCAL_MEDIA_STREAM);
          video.play();

          var call = peer.call(remote_user_id, lOCAL_MEDIA_STREAM); // call to that id
          call.on('stream', function(stream) {
              // `stream` is the MediaStream of the remote peer.
              // Here you'd add it to an HTML video/canvas element.
              console.log("receive stream from remote user\n");
              var other_video = document.getElementById("other_video");
              other_video.src = window.URL.createObjectURL(stream);
              other_video.play();
            });

          peer.on('call', function(call) {  // answer call
            // Answer the call, providing our mediaStream
            call.answer(lOCAL_MEDIA_STREAM);
            call.on('stream', function(stream) { // 接收
              // `stream` is the MediaStream of the remote peer.
              // Here you'd add it to an HTML video/canvas element.
              console.log("receive stream from remote user\n");
              var other_video = document.getElementById("other_video");
              other_video.src = window.URL.createObjectURL(stream);
              other_video.play();
            });
          });
        
        }

      });
    </script>
</html>