<?php
require_once(realpath(dirname(__FILE__) . "/../config/config.php"));
?>

<!-- Немного стиля для вывода строк редактора -->
<style>
    #edit-form {
        display: flex;
        flex-flow: column;
    }
</style>

<script type="text/javascript">
    const state = {
        editorAction: 0
    };

    function onSelectionChange(select) {
        if (select.value == "AddField") {
            state.editorAction = select.selectedIndex;
            const form = document.getElementById("edit-form");
            form.textContent = '';
            fetch(`/page/editor_forms.php?action=${state.editorAction}&table=<?php echo $_GET["table"]; ?>`).then(async data => {
                console.log(state.editorAction);
                const body = await data.text();
                form.innerHTML = body;
            });
        }
        if (select.value == "DelField") {
            const form = document.getElementById("edit-form");
            form.textContent = '';
            state.editorAction = select.selectedIndex;
            fetch(`/page/editor_forms.php?action=${state.editorAction}&table=<?php echo $_GET["table"]; ?>`).then(async data => {
                console.log(state.editorAction);
                const body = await data.text();
                form.innerHTML = body;
            });
        }
        if (select.value == "Report") {
            const form = document.getElementById("edit-form");
            form.textContent = '';
            state.editorAction = select.selectedIndex;
            fetch(`/page/editor_forms.php?action=${state.editorAction}&table=null; ?>`).then(async data => {
                console.log(state.editorAction);
                const body = await data.text();
                form.innerHTML = body;
            });
        }
        if (select.value == "EditField") {
            const form = document.getElementById("edit-form");
            form.textContent = '';
            state.editorAction = select.selectedIndex;
            fetch(`/page/editor_forms.php?action=${state.editorAction}&table=<?php echo $_GET["table"]; ?>`).then(async data => {
                console.log(state.editorAction);
                const body = await data.text();
                form.innerHTML = body;
            });
        }
    }
</script>

<div>
    <select onchange="onSelectionChange (this)" require>
        <option required selected disabled></option>
        <option value="AddField">Добавить запись</option>
        <option value="DelField">Удалить запись</option>
        <option value="Report">Отчёт</option>
        <option value="EditField">Изменить</option>
    </select>
    <form action="/page/main.php?table=<?php echo $_GET["table"]; ?>" id="edit-form" method="POST">

    </form>
</div>