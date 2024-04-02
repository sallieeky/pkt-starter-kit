import * as am4core from "@amcharts/amcharts4/core";

function am4themes_pkt_themes(target) {
    if (target instanceof am4core.ColorSet) {
        target.list = [
            am4core.color("#9dcaf2"),
            am4core.color("#feb57f"),
            am4core.color("#bce29e"),
            am4core.color("#d9a9de"),
        ];
    }
}

export default am4themes_pkt_themes;