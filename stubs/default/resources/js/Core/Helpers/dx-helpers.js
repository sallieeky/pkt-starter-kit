const allMode = "page";
const dataGridAction = "index";

function isNotEmpty(value) {
    return value !== undefined && value !== null && value !== "";
};

export function dxLoad(route){
    return function (loadOptions) {
        let params = "?";
        ["skip", "take", "requireTotalCount", "sort", "filter"].forEach(
            function (i) {
                if (i in loadOptions && isNotEmpty(loadOptions[i])) {
                    params += `${i}=${JSON.stringify(loadOptions[i])}&`;
                }
            }
        );
        params = params.slice(0, -1);

        if (dataGridAction == "select.all") {
            if (allMode == "allPages") {
                return axios.get(route, { params: params })
                    .then((response) => {
                        data = response.data;
                    })
                    .catch((error) => { });
            } else {
                dataGridAction = "index";
            }
        } else {
            return axios.get(route + params)
                .then((response) => {
                    return response.data;
                })
                .catch((error) => { });
        }
    };
}