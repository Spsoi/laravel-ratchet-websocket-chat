<body>
  <div class="container">
    <div> @if($chat_id == 1) <h3>Kомната 1</h3> @elseif($chat_id == 2) <h3>Комната 2</h3> @else <h3>Kомнатa 3</h3> @endif </div>
    <div class="row">
      <div class="col-3"> Пользователи онлайн 
        <ul id="users"></ul>
      </div>
      <div class="col-5"> Сообщения <div id="messages"></div>
      </div>
      <div class="col-3"> <input type="text" id="text">
        <button id="send" onclick="send()">0тправить</button>
      </div>
    </div>
  </div>
  

  <script>
    var socket = new WebSocket("ws://192.168.1.102:8080");

    socket.onopen = function() {
      socket.send(JSON.stringify({
        message: "new room",
        chat: {
          id:  "{{$chat_id}}",
        },
        from: {
            first_name: "{{$name}}", // Имя пользователя
        },
        text: "{{$room_name}}"
      }));
      console.log('Соединение установлено');
    };

    socket.onclose = function(event) {};

    socket.onmessage = function(event) {
      var json = JSON.parse(event.data);
      if (json.message === 'connection') {
          const deleteElement = document.querySelector('#users');
          deleteElement.innerHTML = '';
          json.users.map(function (item){
            var users = document.getElementById('users');
            let liFirst = document.createElement('li');
            liFirst.innerHTML =  "<li><span>"+item+"</span></li>";
            users.prepend(liFirst);
        });
      } else if (json.message == 'message') {
          var messages = document.getElementById('messages');
          let pFirst = document.createElement('p');
          pFirst.innerHTML =  "<b>"+json.from.first_name+"</b> "+json.text;
          messages.prepend(pFirst);
      }
    };
    
    socket.onerror = function(error) {
      alert("Oшибка " + error.message); 
    };


    function send() {
      var text = document.getElementById('text').value;
      socket.send(JSON.stringify({
        message: "new message",
        chat: {
          id: "{{$chat_id}}",
        },
        text: text
      }));
    }
  </script>