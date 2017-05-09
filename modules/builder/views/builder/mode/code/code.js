/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $, ace, modelist */

app.controller("Code", function($scope, $http, $timeout, $q) {
    $scope.active = null;
    window.mode.code = $scope;
    $scope.editor = ace.edit("code-editor");
    $scope.editor.setTheme("ace/theme/monokai");
    $scope.editor.$blockScrolling = Infinity;
    $scope.editor.setOptions({
        autoScrollEditorIntoView: true,
        enableEmmet: true,
        fontSize: "13px"
    });
    $scope.editor.commands.bindKey("Command+L", null)
    $scope.editor.commands.addCommand({
        name: "FormatCode",
        bindKey: {
            win: "Ctrl-Alt-f",
            mac: "Command-Alt-f"
        },
        exec: function(editor) {
            var item = $scope.active;
            item.loading = true;
            $http({
                method: 'POST',
                url: Yii.app.createUrl('/builder/mode/code.format'),
                transformResponse: undefined,
                data: editor.getValue()
            }).then(function(res) {
                item.loading = false;
                editor.setValue(res.data);
            }).catch(function() {
                item.loading = false;
            })
        }
    })

    window.addEventListener("resize", function(e) {
        $scope.editor.resize(true);
        $timeout(function() {
            $scope.editor.resize(true);
        }, 200);
        $timeout(function() {
            $scope.editor.resize(true);
        }, 600);
    }, false);
    
    var changeTimeout = false;
    
    $scope.editor.on('change', function() {
        if (changeTimeout) {
            $timeout.cancel(changeTimeout);
            changeTimeout = false;
        }
        changeTimeout = $timeout(function() {
            $scope.active.unsaved = !$scope.editor.session.getUndoManager().isClean();
            $scope.active.code.content = $scope.editor.getValue();
            if ($scope.active.unsaved) {
                $scope.active.code.status = 'Unsaved';
            }
            else {
                $scope.active.code.status = 'Ready';
            }
            
            if (typeof $scope.active.size != "undefined" && $scope.active.size <= 120) {
                window.tabs.findTab($scope.active, function(idx) {
                    var item = window.tabs.stripItem(window.tabs.list[idx]);
                    window.builder.set('tabs.list.' + item.id, item);
                });
            }
        }, 300);
    });

    $scope.blankSession = ace.createEditSession("");

    $scope.open = function(item) {
        if (!item.code) {
            item.code = {}
        }

        if (!item.code.session) {
            $scope.openInEditor(null);
        }
        else {
            $scope.openInEditor(item);
            if (!item.loading) {
                return true;
            }
        }

        $http({
            url: Yii.app.createUrl('/builder/mode/code.index&f=' + item.d),
            method: 'GET',
            transformResponse: undefined
        }).then(function(res) {
            if (!!item.code && item.code.content) {
                var newcontent = item.code.content;
            }
            var cursor = false;
            if (!!item.code && !!item.code.cursor) {
                cursor = item.code.cursor;
            }
            
            item.code = {
                content: res.data
            };
            
            if (cursor) {
                item.code.cursor = cursor
            }
            
            item.loading = false;
            if (window.tabs.active.id == item.id) {
                if (newcontent != item.code.content) { // if code on server is different on our local change
                    $scope.openInEditor(item, newcontent);
                }
                else {
                    $scope.openInEditor(item);
                }
            }
        });
    }

    $scope.openInEditor = function(item, newcontent) {
        if (!item) {
            $scope.active = {
                code: {}
            };
            $scope.active.code.status = 'Loading';
            $scope.editor.setSession($scope.blankSession);
            $scope.editor.setValue("");
            return false;
        }
        if (!item.code) return false;

        if (!item.code.session) {
            var modelist = ace.require("ace/ext/modelist")
            var mode = modelist.getModeForPath(item.n + '.' + item.ext).mode;
            item.code.session = ace.createEditSession(item.code.content);
            item.code.session.setUseWorker(false);
            item.code.session.setMode(mode);
            item.code.session.setUseWrapMode(true);

            if (item.ext == 'php') {
                $scope.editor.setOptions({
                    'enableEmmet': false
                });
            }

            item.code.session.selection.on('changeCursor', function(e) {
                $timeout(function() {
                    item.code.cursor = $scope.editor.selection.getCursor();

                    if (item.ext == 'php') {
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
                            $scope.editor.setOptions({
                                'enableEmmet': false
                            });
                        }
                        else {
                            $scope.editor.setOptions({
                                'enableEmmet': true
                            });
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
        delete $scope.active.code; 
        delete $scope.active.unsaved;
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

        var url = Yii.app.createUrl('builder/mode/code.save', {
            f: $scope.active.d,
            h: $scope.active.unsaved ? 1 : 0
        });
        $scope.active.code.status = 'Saving...';
        $scope.active.loading = true;
        $scope.active.unsaved = false;
        $http({
            method: 'POST',
            url: url,
            uploadEventHandlers: {
                progress: function(e) {
                    if (e.lengthComputable) {
                        Math.ceil((e.loaded / e.total) * 100); // percentage
                    }
                }
            },
            data: {
                content: $scope.active.code.content
            }
        }).then(function(res) {
            $scope.active.loading = false;
            if (res.data == '1') {
                $scope.active.code.status = 'Saved';
                
                var item = window.tabs.stripItem($scope.active)
                window.builder.set('tabs.list.' + item.id, item);
                // store it in localstorage
                // var item = JSON.parse(store['tabs-' + $scope.active.d]);
                // store['tabs-' + $scope.active.d] = JSON.stringify(item);
                // delete store['tabs|code-' + $scope.active.d];
            }
            else {
                $scope.active.code.status = 'Save failed';
            }
        }).catch(function(res) {
            $scope.active.code.status = 'Save failed';
            $scope.active.loading = false;
        })
    }
});