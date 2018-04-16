(function () {
    'use strict';
    /*
var token = [];
getToken();

function getToken () {    
    $.post("https://www.tiendanube.com/apps/authorize/token",{
        client_id: "715",
        client_secret: "hAHe8O3phu66ZVT7Fq60aVcN5DoZIrSiRwSV0zd8BKJsesej",
        grant_type:"authorization_code",
        code:"b0b016e753fa3718a350648232f02e9b350613ae"
    },function(data){
        token.push(data);
        console.log(data);
    });
}
*/

var app = angular.module('todoApp', [], function() {
    

});
 
app.controller('todoController', function($scope, $http) {
 
	$scope.todos = [];
	$scope.loading = false;
 
	$scope.init = function() {
		//getToken();
    }
    


/*
        $.post("https://www.tiendanube.com/apps/authorize/token",{
            client_id: "715",
            client_secret: "hAHe8O3phu66ZVT7Fq60aVcN5DoZIrSiRwSV0zd8BKJsesej",
            grant_type:"authorization_code",
            code:"89d93f584106ad0593d7a3c4c23bfd753f92e25b"
        },function(data){
            $scope.todo.push(data);
            console.log(data);
        })
       
*/
function getToken(){ 
    
    var req = {
        method: 'POST',
        url: 'https://www.tiendanube.com/apps/authorize/token/',        
        data: { 
            client_id: "715",
            client_secret: "hAHe8O3phu66ZVT7Fq60aVcN5DoZIrSiRwSV0zd8BKJsesej",
            grant_type:"authorization_code",
            code:"30b72dde8148f117ca702f85a699ae77e749ee6d"
        },
        headers: { 'X-Requested-With' :'XMLHttpRequest'}
       }
    
    $http(req).then(
        function(data){
            $scope.todo.push(data);
            console.log($scope);
            console.log("success");
            $scope.loading = false;
        }, 
        function(error){
            console.log("todo rot0oasadhusadhidsakjdsakjsajk")
            console.log(error);    
        });
/*

$http.post("https://www.tiendanube.com/apps/authorize/token/",{
    client_id: "715",
    client_secret: "hAHe8O3phu66ZVT7Fq60aVcN5DoZIrSiRwSV0zd8BKJsesej",
    grant_type:"authorization_code",
    code:"30b72dde8148f117ca702f85a699ae77e749ee6d"
}).then(
    function(data){
        $scope.todo.push(data);
        console.log($scope);
        console.log("success");
        $scope.loading = false;
}, 
    function(error){
        console.log("todo roto")
        console.log(error);
    }
);

*/
}

            

/*
        $scope.loading = true;
        $http.post("http://www.tiendanube.com/apps/authorize/token/",{
            client_id: "715",
            client_secret: "hAHe8O3phu66ZVT7Fq60aVcN5DoZIrSiRwSV0zd8BKJsesej",
            grant_type:"authorization_code",
            code:"5feb9f74c6fbc55ec1fd8b7a232c8e5e52df6829"
        }).then(
            function(data){
                $scope.todo.push(data);
                console.log($scope);
                console.log("success");
                $scope.loading = false;
        }, 
            function(error){
                console.log("todo roto")
                console.log(error);
            }
        );
        
    }
*/
 
	$scope.addTodo = function() {
				$scope.loading = true;
 
		$http.post('/api/todos', {
			title: $scope.todo.title,
			done: $scope.todo.done
		}).success(function(data, status, headers, config) {
			$scope.todos.push(data);
			$scope.todo = '';
				$scope.loading = false;
 
		});
	};
 
	$scope.updateTodo = function(todo) {
		$scope.loading = true;
 
		$http.put('/api/todos/' + todo.id, {
			title: todo.title,
			done: todo.done
		}).success(function(data, status, headers, config) {
			todo = data;
				$scope.loading = false;
 
		});;
	};
 
	$scope.deleteTodo = function(index) {
		$scope.loading = true;
 
		var todo = $scope.todos[index];
 
		$http.delete('/api/todos/' + todo.id)
			.success(function() {
				$scope.todos.splice(index, 1);
					$scope.loading = false;
 
			});;
	};
 
	$scope.init();
 
});
})()
