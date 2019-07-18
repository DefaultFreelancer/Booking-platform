import  axios from "axios";

export default {
    data(){
        return{
            inputFields: [],
            hidePreLoader: true,
            isActive: false,
            selectedItemId: '',
            deleteID:'',
            deleteIndex:'',
            updateIndex:'',
        }
    },
    mounted(){

        let instance = this;
        this.$hub.$on('selectedDeletableId',function (id,index){
            instance.deleteID = id;
            instance.deleteIndex = index;
        });
    },
    methods: {

        axiosGet(url, onSuccess, onError) {

            axios.get(window.appConfig.appUrl+url)
                .then(function (response) {
                    if(onSuccess) onSuccess(response);

                }.bind(this))
                .catch(function (error) {
                    if(onError) onError(error);
                })
        },
        axiosGetWithData(url, data, onSuccess, onError) {

            axios.get(window.appConfig.appUrl+url,data)
                .then(function (response) {
                    if(onSuccess) onSuccess(response);

                }.bind(this))
                .catch(function (error) {
                    if(onError) onError(error);
                }
            )
        },
        axiosPost(url, postData, onSuccess, onError) {

            axios.post(window.appConfig.appUrl+url,postData)
                .then(function (response) {
                    if(onSuccess) onSuccess(response);
                }.bind(this))
                .catch(({response}) => {
                    if(onError) onError(response);
                });
        },
        axiosPut(url, postData, onSuccess, onError) {

            axios.put(window.appConfig.appUrl+url,postData)
                .then(function (response) {
                    if(onSuccess) onSuccess(response);

                }.bind(this))
                .catch(({response}) => {
                    if(onError) onError(response);
                });
        },

        redirect(route){
            location.href = window.appConfig.appUrl+route;
        },
    }

};


