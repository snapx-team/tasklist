<template>
    <div class="wrapper">
        <span class="cloud-1"></span>
        <span class="cloud-2"></span>
        <span class="cloud-3"></span>
        <span class="cloud-4"></span>
        <span class="cloud-5"></span>
        <span class="cloud-6"></span>
        <span class="cloud-7"></span>
        <span class="cloud-9"></span>
        <span class="cloud-10"></span>
        <span class="cloud-11"></span>
        <span class="cloud-12"></span>
        <span class="car"></span>
        <span class="sun"></span>
        <span class="car car-2"></span>
        <span class="car car-3"></span>
        <span class="car car-4"></span>
    </div>
</template>


<script>
import city from '../../../assets/cityScapeBackgroundImages/city.svg';
import car from '../../../assets/cityScapeBackgroundImages/car.svg';
import cloud from '../../../assets/cityScapeBackgroundImages/cloud.svg';
import cloudShadow from '../../../assets/cityScapeBackgroundImages/cloud-shadow.svg';
import sun from '../../../assets/cityScapeBackgroundImages/sun.svg';

export default {
    name: 'CityScapeBackground',
    data() {
        return {
            city: `url(${city})`,
            car: `url(${car})`,
            cloud: `url(${cloud})`,
            cloudShadow: `url(${cloudShadow})`,
            sun: `url(${sun})`,
        };
    },
}
</script>


<style scoped>

.wrapper {
    height: 279px;
    width: 100%;
    max-width: 1400px;
    position: relative;
    overflow: hidden;
}

.wrapper [class*="cloud"] {
    background: url("/vendor/tasklist/images/cloud.svg") no-repeat 0 0;
    position: absolute;
    display: block;
    z-index: 1;
    top: 85px;
    left: -20px;
    width: 18px;
    height: 15px;
    animation: cloudAnimation 60s linear 0s infinite none running;
}

.wrapper .cloud-2 {
    animation-delay: 5s;
    top: 15px;
}

.wrapper .cloud-3 {
    animation-delay: 12s;
    top: 40px;
}

.wrapper .cloud-4 {
    background: url("/vendor/tasklist/images/cloud.svg") no-repeat 0 0;
    animation-delay: 18s;
    top: 85px;
}

.wrapper .cloud-5 {
    animation-delay: 25s;
    top: 15px;
}

.wrapper .cloud-6 {
    animation-delay: 28s;
    top: 50px;
}

.wrapper .cloud-7 {
    animation-delay: 33s;
    top: 90px;
}

.wrapper .cloud-9 {
    animation-delay: 44s;
    top: 35px;
}

.wrapper .cloud-10 {
    animation-delay: 50s;
    top: 70px;
}

.wrapper .cloud-11 {
    background: url("/vendor/tasklist/images/cloud-shadow.svg") no-repeat 0 0;
    animation-delay: 56s;
    top: 15px;
}

.wrapper .cloud-12 {
    animation-delay: 60s;
    top: 55px;
}

.wrapper::before {
    content: "";
    background: url("/vendor/tasklist/images/city.svg") no-repeat 0 0;
    height: 279px;
    width: 1400px;
    position: absolute;
    z-index: 2;
    top: 0;
    left: 0;
}

.wrapper .sun {
    background: url("/vendor/tasklist/images/sun.svg") no-repeat 0 0;
    height: 30px;
    width: 30px;
    position: absolute;
    z-index: 1;
    top: 30px;
    left: -35px;
    animation: sunAnimation 120s linear 10s infinite none running;
}

.wrapper .car {
    background: url("/vendor/tasklist/images/car.svg") no-repeat 0 0;
    height: 23px;
    width: 35px;
    position: absolute;
    top: 215px;
    right: -50px;
    animation: carAnimation 20s linear 0s infinite none running;
}

.wrapper .car-2 {
    animation-delay: 5s;
    top: 255px;
}

.wrapper .car-3 {
    animation-delay: 10s;
    top: 255px;
}

.wrapper .car-4 {
    animation-delay: 15s;
    animation-duration: 15s;
    top: 230px;
}

@keyframes carAnimation {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(-1435px, 0);
    }
}

@keyframes cloudAnimation {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(1450px, 0);
    }
}

@keyframes sunAnimation {
    0% {
        transform: translate(0, 0) rotate(0deg);
    }
    100% {
        transform: translate(1450px, 0) rotate(359deg);
    }
}


</style>
