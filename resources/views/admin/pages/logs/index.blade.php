<!DOCTYPE>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Laravel log</title>
    <script language="Javascript" type="text/javascript" src="{{ asset('assets/libs/edit_area/edit_area_full.js') }}"></script>
    <script language="Javascript" type="text/javascript">
        // initialisation
        editAreaLoader.init({
            id: "example_1"	// id of the textarea to transform
            , start_highlight: true	// if start with highlight
            , allow_resize: "both"
            , allow_toggle: true
            , word_wrap: true
            , language: "en"
            , syntax: "php",
            min_height: 850
        });

        editAreaLoader.init({
            id: "example_2"	// id of the textarea to transform
            ,
            start_highlight: true
            ,
            allow_toggle: false
            ,
            language: "en"
            ,
            syntax: "html"
            ,
            toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help"
            ,
            syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql,basic,pas,brainfuck"
            ,
            is_multi_files: true
            ,
            EA_load_callback: "editAreaLoaded"
            ,
            show_line_colors: true
        });

        editAreaLoader.init({
            id: "example_3"	// id of the textarea to transform
            ,
            start_highlight: true
            ,
            font_size: "8"
            ,
            font_family: "verdana, monospace"
            ,
            allow_resize: "y"
            ,
            allow_toggle: false
            ,
            language: "fr"
            ,
            syntax: "css"
            ,
            toolbar: "new_document, save, load, |, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
            ,
            load_callback: "my_load"
            ,
            save_callback: "my_save"
            ,
            plugins: "charmap"
            ,
            charmap_default: "arrows"

        });

        editAreaLoader.init({
            id: "example_4"	// id of the textarea to transform
            //,start_highlight: true	// if start with highlight
            //,font_size: "10"
            , allow_resize: "no"
            , allow_toggle: true
            , language: "de"
            , syntax: "python"
            , load_callback: "my_load"
            , save_callback: "my_save"
            , display: "later"
            , replace_tab_by_spaces: 4
            , min_height: 350
        });

    </script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-6 col-md-12">
            <div class="contain-form">
                <div class="form">
                        <textarea id="example_1" style="width: 100%; height: 100%" readonly>
                            @foreach($logCollection as $log)
                                {{ str_replace('&gt;', '>', $log['content']) }}
                            @endforeach
                        </textarea>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
