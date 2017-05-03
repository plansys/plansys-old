/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $, ace, modelist */

app.controller("Code", function($scope, $http, $timeout, $q) {
     $scope.active = null;
     window.code = $scope;
     $scope.editor = ace.edit("code-editor");
     $scope.editor.setTheme("ace/theme/monokai");
     $scope.editor.$blockScrolling = Infinity;
     $scope.editor.setOptions({
          autoScrollEditorIntoView: true,
          enableEmmet: true
     });
     $scope.editor.commands.bindKey("Command+L", null)

     window.addEventListener("resize", function(e) {
          $timeout(function() {
               $scope.editor.resize(true);
          });
     }, false);

     $scope.editor.on('change', function() {
          $timeout(function() {
               $scope.active.unsaved = !$scope.editor.session.getUndoManager().isClean();
               $scope.active.code.content = $scope.editor.getValue();

               if ($scope.active.unsaved) {
                    $scope.active.code.status = 'Unsaved';
               }
               else {
                    $scope.active.code.status = 'Ready';
               }
          });
     });
     
     $scope.blankSession = ace.createEditSession("");
     
     $scope.open = function(item, newcontent) {
          if (!item) {
               $scope.active = {code: {}};
               $scope.active.code.status = 'Loading';
               $scope.editor.setSession($scope.blankSession);
               $scope.editor.setValue("");
               return false;
          }
          if (!item.code) return false;

          if (!item.code.session) {
               var ext = item.n.split(".");
               ext = ext[ext.length - 1];
               
               var modelist = ace.require("ace/ext/modelist")
               var mode = modelist.getModeForPath(item.n).mode;
               item.code.session = ace.createEditSession(item.code.content);
               item.code.session.setMode(mode);
               item.code.session.setUseWrapMode(true);
               
               if (ext == 'php') {
                    $scope.editor.setOptions({'enableEmmet': false});
               }
               
               item.code.session.selection.on('changeCursor', function(e) {
                    $timeout(function() {
                         item.code.cursor = $scope.editor.selection.getCursor();
                         
                         if (ext == 'php') {
                              var max = Math.max(0, (item.code.cursor.row - 500));
                              var foundphp = false;
                              var foundphpcloser = false;
                              for (var i = item.code.cursor.row; i >= max; i--) {
                                   var line = item.code.session.getLine(i);
                                   
                                   if (i == item.code.cursor.row) {
                                        line = line.substr(0, item.code.cursor.column);
                                   }
                                   if (line.lastIndexOf('?>') >= 0) {
                                        foundphpcloser = true;
                                   }
                                   
                                   if (line.lastIndexOf('<?php') >= 0) {
                                        if (!foundphpcloser) {
                                             foundphp = true;
                                        }
                                        break;
                                   }
                              }
                              if (foundphp) {
                                   $scope.editor.setOptions({'enableEmmet': false});
                              } else {
                                   $scope.editor.setOptions({'enableEmmet': true});
                              }
                         }
                         
                         item.code.cursor.row++;
                         item.code.cursor.column++;
                    });
               });
               $timeout(function() {
                    if (typeof newcontent == "string") {
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
          $scope.editor.setSession(null);
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