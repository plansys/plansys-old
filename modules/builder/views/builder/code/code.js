/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $, ace */

app.controller("Code", function($scope, $http, $timeout, $q) {
     $scope.active = null;
     window.code = $scope;
     // var store = window.localStorage;

     window.addEventListener("storage", function(e) {
          console.log(e);
     }, false);

     $scope.editor = ace.edit("code-editor");
     $scope.editor.setTheme("ace/theme/monokai");
     $scope.editor.$blockScrolling = Infinity;
     $scope.editor.setOptions({
          wrap: true,
          enableEmmet: true
     });
     $scope.editor.commands.bindKey("Command+L", null)

     $scope.editor.on('change', function() {
          $timeout(function() {
               $scope.active.unsaved = !$scope.editor.session.getUndoManager().isClean();
               $scope.active.code.content = $scope.editor.getValue();

               if ($scope.active.unsaved) {
                    $scope.active.code.status = 'Unsaved';

                    // var item = JSON.parse(store['tabs-' + $scope.active.d]);
                    // store['tabs-' + $scope.active.d] = JSON.stringify(item);
                    // store['tabs|code-' + $scope.active.d] = JSON.stringify({
                    //      content: $scope.active.code.content,
                    //      cursor: $scope.active.code.cursor
                    // });
               }
               else {
                    $scope.active.code.status = 'Ready';
                    // if (store['tabs|code-' + $scope.active.d]) {
                    //      delete store['tabs|code-' + $scope.active.d];
                    // }
               }
          });
     });

     $scope.open = function(item, newcontent) {
          if (!item.code) return false;

          if (!item.code.session) {
               var ext = item.n.split(".");
               var extpath = "";
               ext = ext[ext.length - 1];

               var exts = {
                    'html': "ace/mode/html",
                    'js': "ace/mode/javascript",
                    'go': "ace/mode/golang",
                    'gitignore': "ace/mode/gitignore",
                    'css': "ace/mode/css",
                    'php': "ace/mode/php",
                    'json': "ace/mode/json",
               }

               if (exts[ext]) {
                    item.code.session = ace.createEditSession(item.code.content, exts[ext]);
               }
               else {
                    item.code.session = ace.createEditSession(item.code.content);
               }

               item.code.session.setUseWrapMode(true);

               item.code.session.selection.on('changeCursor', function(e) {
                    $timeout(function() {
                         item.code.cursor = $scope.editor.selection.getCursor();
                         item.code.cursor.row++;
                         item.code.cursor.column++;
                    });
               });
               $timeout(function() {
                    if (newcontent) {
                         $scope.editor.setValue(newcontent);
                    }
                    $scope.editor.focus();
                    if (!item.code.cursor) {
                         $scope.editor.gotoLine(0, 0);
                         item.code.cursor = {
                              row: 1,
                              column: 1
                         }
                    }
                    else {
                         $scope.editor.gotoLine(item.code.cursor.row, item.code.cursor.column);
                    }
               });
          }

          $scope.active = item;
          $scope.active.code.status = 'Ready';
          $scope.editor.setSession(item.code.session);
     }
     $scope.close = function() {
          $scope.active = null;
     }
     $scope.gotoLine = function(line, e) {
          if (e.keyCode == 13) {
               $timeout(function() {
                    $scope.editor.gotoLine(line);
                    $scope.editor.focus();
               });
          }
     }

     $scope.save = function() {
          if ($scope.active.loading) {
               return;
          }
          
          var url = Yii.app.createUrl('builder/code/save', {
               f: $scope.active.d,
               h: $scope.active.unsaved ? 1 : 0
          });
          $scope.active.code.status = 'Saving...';
          $scope.active.loading = true;
          $scope.active.unsaved = true;
          $http({
               method: 'POST',
               url: url,
               uploadEventHandlers: {
                    progress: function(e) {
                         if (e.lengthComputable) {
                              $scope.active.code.status = "Saving (" + Math.ceil((e.loaded / e.total) * 100) + "%)...";
                         }
                    }
               },
               data: {
                    content: $scope.active.code.content
               }
          }).then(function(res) {
               if (res.data == 'success') {
                    $scope.active.code.status = 'Saved';
                    $scope.active.unsaved = false;

                    // store it in localstorage
                    // var item = JSON.parse(store['tabs-' + $scope.active.d]);
                    // store['tabs-' + $scope.active.d] = JSON.stringify(item);
                    // delete store['tabs|code-' + $scope.active.d];
               }
               else {
                    $scope.active.code.status = 'Save failed. (' + res.data.split(":").pop().trim() + ')';
               }
               $scope.active.loading = false;
          }).catch(function(res) {
               $scope.active.code.status = 'Save failed. (' + res.data + ')';
               $scope.active.loading = false;
          })
     }
});