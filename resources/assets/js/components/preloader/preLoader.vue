<template>
    <div class="center-align">
        <div class="preloader-wrapper big active">
            <div class="spinner-layer" :class="{'spinner-blue-only':loderType=='load' ,'spinner-red-only':loderType=='delete'}">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:['loderType','hideLoader'],

    }
</script>
