/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $, ace, modelist */

app.controller("Code", function($scope, $http, $timeout, $q) {
    $scope.active = null;
    window.mode.code = $scope;

    window.addEventListener("resize", function(e) {
        if ($scope.editor) {
            $scope.editor.resize(true);
            $timeout(function() {
                $scope.editor.resize(true);
            }, 200);
            $timeout(function() {
                $scope.editor.resize(true);
            }, 600);
        }
    }, false);

    $scope.initEditor = function() {
        $("#code-editor-container").append('<div id="code-editor"></div>');
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

        var changeTimeout = false;

        $scope.editor.on('change', function() {
            $scope.active.code.content = $scope.editor.getValue();

            if (changeTimeout) {
                $timeout.cancel(changeTimeout);
                changeTimeout = false;
            }
            changeTimeout = $timeout(function() {
                if ($scope.active.code.formatting) {
                    delete $scope.active.code.formatting;
                }
                else {
                    $scope.active.unsaved = !$scope.editor.session.getUndoManager().isClean();
                }
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
    }
    $scope.initEditor();

    $scope.$watch('active.editing', function(n, o) {
        if (!$scope.active || !$scope.active.editing) return;

        if ($scope.active.editing.cid == window.builder.statusbar.me.cid) {
            $scope.active.code.status = $scope.active.unsaved ? "Unsaved" : "Ready";
        }
        else {
            $scope.active.code.status = "Can't edit";
        }
    })

    $scope.$watch('active.unsaved', function(n, o) {
        if (!$scope.active || !$scope.active.code) return;

        $scope.active.code.status = $scope.active.unsaved ? "Unsaved" : "Ready";
    })

    $scope.$watch('active.code.status', function(n, o) {
        switch (n) {
            case "Saved":
                $timeout(function() {
                    $scope.active.code.status = "Ready";
                }, 3000);
                break;
        }
    }, true)

    $scope.getStatusTooltip = function(active) {
        if (!active || !active.code) return "";
        if (active.loading) {
            return 'Please wait...';
        }

        switch (active.code.status) {
            case 'Ready':
                return 'Ctrl + S to save';
                break;
            case 'Unsaved':
                return 'Ctrl + S to save';
                break;
            case 'Save Failed':
                return active.code.errors;
                break;
        }
    }
    $scope.getStatusIcon = function(active) {
        if (!active || !active.code) return "";
        if (active.loading) {
            return 'fa-refresh fa-spin';
        }

        switch (active.code.status) {
            case 'Ready':
            case 'Saved':
                return 'fa-check';
                break;
            case 'Unsaved':
                return 'fa-fire';
                break;
            case 'Can\'t edit':
                return 'fa-ban';
                break;
            case 'Save Failed':
                return 'fa-exclamation-triangle';
                break;
        }
    }

    $scope.getStatusColor = function(active) {
        if (!active || !active.code) return "";
        if (active.loading) {
            return '#999';
        }

        switch (active.code.status) {
            case 'Ready':
            case 'Saved':
                return 'green';
                break;
            case 'Save Failed':
            case 'Unsaved':
            case 'Can\'t edit':
                return 'red';
                break;
        }
    }

    $scope.blankSession = ace.createEditSession("");
    $scope.open = function(item, options) {
        if (!$scope.editor) {
            $scope.initEditor();
        }
        
        $scope.editor.setSession($scope.blankSession);
        $scope.editor.setValue("");
        
        if (!item) { // when we dont have the item to open...
            $scope.active = {
                code: {}
            };
            $scope.active.code.status = 'Loading';
            return false;
        }

        if (!item.code) {
            item.code = {}
        }

        if (!item.code.session) {
            options.reload = true;
        }

        if (!options.reload) {
            $scope.openInEditor(item, options);
        }
        else {
            $http({
                url: Yii.app.createUrl('/builder/mode/code.index&f=' + item.d),
                method: 'GET',
                transformResponse: undefined
            }).then(function(res) {
                var cursor = false;
                if (!!item.code && !!item.code.cursor) {
                    cursor = item.code.cursor;
                }

                if (cursor) {
                    item.code.cursor = cursor
                }

                item.code.content = res.data;
                $scope.openInEditor(item, options);
            });
        }
    }

    $scope.openInEditor = function(item, options) {
        if (!item || !item.code || typeof item.code.content != "string") {
            alert("Previous content not found!! coding error!");
            return false;
        }

        if (options.reload && typeof options.content == "string") {
            if (item.code.session) {
                delete item.code.session;
            }
        }

        // initiate ace session
        if (!item.code.session) {
            var modelist = ace.require("ace/ext/modelist")
            var mode = modelist.getModeForPath(item.n + '.' + item.ext).mode;

            // set default content based on options
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
                // overwrite content, so it can be undo-ed (ctrl-z)
                if (options.reload && typeof options.content == "string") {
                    if (options.content != item.code.content) {
                        // make the user choose between file on server or file on editor
                        $scope.editor.setValue(options.content);
                        $scope.active.unsaved = true;
                    }
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
                item.loading = false;
            });
        } else {
            item.loading = false;
        }

        
        $scope.active = item;
        $scope.active.code.status = $scope.active.unsaved ? 'Unsaved' : 'Ready';
        $scope.editor.setSession(item.code.session);
    }
    $scope.close = function(item) {
        // if ($scope.editor) {
        //     $scope.editor.setSession(null);
        //     $scope.editor.destroy();
        //     var el = $scope.editor.container;
        //     if (el) {
        //         el.parentNode.removeChild(el);
        //     }
        //     $scope.editor.container = null;
        //     $scope.editor.renderer = null;
        //     $scope.editor = null;
        // }
        $timeout(function() {
            if (item.code.session) {
                delete item.code.session;
            }
            delete item.code;
            delete item.unsaved;
        });
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
            $scope.active.unsaved = false;
            $scope.active.code.formatting = true;
            $scope.active.code.status = 'Saved';

            $scope.active.code.content = res.data;
            $scope.editor.setValue(res.data);
            $scope.editor.gotoLine($scope.active.code.cursor.row, $scope.active.code.cursor.column);

            var item = window.tabs.stripItem($scope.active)
            window.builder.set('tabs.list.' + item.id, item);
        }).catch(function(res) {
            $scope.active.code.status = 'Save Failed';
            console.log(res);
            $scope.active.loading = false;
        })
    }
});