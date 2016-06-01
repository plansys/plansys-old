<div class="todo-widget" ng-controller="TodoWidgetController">
    <div class = "properties-header">
        <div ng-click="clear()" ng-if="!loading && $storage.todo.view == 'completed' && count() > 0" 
             class="btn btn-xs btn-default pull-right">
            Clear
        </div>
        <div ng-click="newNote()" ng-if="!loading && $storage.todo.view != 'completed' && !showNew" 
             class="btn btn-xs btn-default pull-right">
            <i class="fa fa-plus"></i> New
        </div>
        <div class="pull-right" ng-if="loading">
            <i class="fa fa-spin fa-refresh"></i>
        </div>
        
        <i class = "fa fa-nm fa-check-square-o"></i>&nbsp;
        Todo List: {{ countText()}} 
    </div>
    <div class="todo-status-container">
        <div class="todo-status-item" ng-class="{
                    active:$storage.todo.view == 'active'
                }" 
             ng-click="$storage.todo.view = 'active'">Active</div>
        <div class="todo-status-item" ng-class="{
                    active:$storage.todo.view == 'completed'
                }" 
             ng-click="$storage.todo.view = 'completed'">Completed</div>
        <div class="todo-status-item" ng-class="{
                    active:$storage.todo.view == 'all'
                }" 
             ng-click="$storage.todo.view = 'all'">All</div>
    </div>
    <div class="hide" id="todo-uid"><?= Yii::app()->user->id ?></div>
    <script class="hide" id="todo-data"><?= json_encode(Yii::app()->todo->list); ?></script>

    <div class="todo-container">
        <div class="widget-item-container">
            <div ng-if="$storage.todo.view == 'all'
                                 || item.status == getStatus()
                                 || (item.note == '' && $storage.todo.view != 'completed')"
                 class="todo-item" ng-class="{
                             checked:item.status == 1
                         }" 
                 ng-repeat="item in $storage.todo.items track by $index">

                <div class="todo-item-check" >
                    <div ng-if="item.type == 'note' && item.note != ''" 
                         class="todo-item-check-detail"
                         ng-click="toggleStatus(item)">
                        <i class="fa todo-unchecked fa-square-o fa-nm"></i>
                        <i class="fa todo-checked fa-check-square-o fa-nm"></i>
                    </div>
                    <?php foreach ($this->ext as $ext): ?>
                        <?= $this->renderCheck($ext); ?>
                    <?php endforeach; ?>
                </div>

                <div class="todo-item-note {{item.type}}" ng-if="item.type == 'note'" >
                    <textarea ng-model="item.note" placeholder="New Todo..." auto-grow
                              ng-change="updateTodo(item)"
                              ng-delay="500"
                              ng-focus="noteFocus(item, $event)"
                              ng-blur="noteBlur(item, $event);"
                              ng-keydown="keyDown($event)"
                              ng-keyup="typeTodo(item, $event, $index)"
                              spellcheck="false"></textarea>
                    <div ng-if="$index == 0" class="todo-shift-enter">
                        <span class="label label-default">
                            Shift + Enter
                        </span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php foreach ($this->ext as $ext): ?>
                    <?= $this->renderNote($ext); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>