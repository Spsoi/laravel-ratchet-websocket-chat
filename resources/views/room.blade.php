<body>
  <div class="container">
    <div> @if($id == 1) <h3>Kомната 1</h3> @elseif($id == 2) <h3>Комната 2</h3> @else <h3>Kомнатa 3</h3> @endif </div>
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
    var socket = new WebSocket("ws://192.168.1.149:8080");

    socket.onopen = function() {
      socket.send('{"message": "new room", "value": "{{$room_name}}", "user": "{{$name}}"}');
      console.log('Соединение установлено')

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
          pFirst.innerHTML =  "<b>"+json.user+"</b> "+json.value;
          messages.prepend(pFirst);
      }
    };
    
    socket.onerror = function(error) {
      alert("Oшибка " + error.message); 
    };


    function send() {
      var text = document.getElementById('text').value;
      // fetch('http://localhost:8000/send_message', {
      //   method: 'POST',
      //   headers: {
      //     'Content-Type': 'application/json;chatset=utf-8'
      //   },
      //   body: JSON.stringify({
      //     text: text
      //   })
      // });
      socket.send('{"message": "new message", "value":"' + text + '"}');
    }
  </script>