# Sublime 配置及插件

## 系统配置

```json
{
    "caret_extra_width": 1,
    "color_scheme": "Packages/Theme - TwoDark/TwoDark.tmTheme",
    "draw_shadows": false,
    "font_face": "yahei mono",
    "font_size": 11,
    "highlight_line": true,
    "highlight_modified_tabs": true,
    "ignored_packages":
    [
        "Markdown",
        "Tag",
        "Vintage"
    ],
    "theme": "TwoDark.sublime-theme",
    "translate_tabs_to_spaces": true,
    "update_check": false,
    "word_wrap": true
}

```

## 插件


### Alignment

> 自动对齐

**快捷键配置**

```json
[
    { "keys": ["ctrl+shift+alt+f"], "command": "alignment" }
]
```

### ConvertToUTF8

GBK编码支持

## DocBlockr

> 注释编辑

**配置**

```json
{
    "jsdocs_extend_double_slash": false,
}
```

### Pretty JSON

**快捷键**

- 格式化JSON： <kbd>ctrl</kbd>+<kbd>alt</kbd>+<kbd>j</kbd>

## Emmet

> 快捷html输入

## GitGutter

> Git状态查看

## Markdown Preview

> Markdown预览

**配置**

```json
{
    "enabled_parsers": ["markdown"],
    "build_action": "browser",
    "enable_highlight": true,
    "enable_autoreload": false,
    "github_inject_header_ids":true
}
```

## SideBarEnhancements

> 右击菜单扩展

## SublimeCodeIntel

> 代码补全工具，支持方法跳转

**配置**

```json
{
    "codeintel_language_settings": {
        "PHP": {
            "php": "D:\\phpStudy\\php\\php-5.5.38\\php.exe",  // PHP路径
            "codeintel_scan_extra_dir": [],
            "codeintel_scan_files_in_project": true,
            "codeintel_max_recursive_dir_depth": 15,
            "codeintel_scan_exclude_dir":["D:\\phpStudy\\php\\php-5.5.38\\ext"] // PHP拓展路径
        }
    }    
}
```

## SyncedSideBar

> 当编辑某个文件时,该插件能在左边栏高亮该文件

## Theme - TwoDark

> 主题