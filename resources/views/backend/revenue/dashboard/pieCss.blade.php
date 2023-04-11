<style>
.pie-container{
  width: 100%;
}
.pie-container.label-text {
  display: block;
  text-align: center;
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 15px;
}

.pie-wrapper {
  position: relative;
  width: 100px;
  height: 50px;
  overflow: hidden;
  margin: auto;
}
.pie-wrapper .arc, .pie-wrapper:before {
  content: '';
  width: 100px;
  height: 50px;
  position: absolute;
  -ms-transform-origin: 50% 0%;
  -webkit-transform-origin: 50% 0%;
  transform-origin: 50% 0%;
  left: 0;
  box-sizing: border-box;
}
.pie-wrapper:before {
  border: 20px solid #E8E8E8;
  border-bottom: none;
  top: 0;
  z-index: 1;
  border-radius: 50px 50px 0 0;
}
.pie-wrapper .arc {
  border: 20px solid #47CF73;
  border-top: none;
  border-radius: 0 0 50px 50px;
  top: 100%;
  z-index: 2;
}
.pie-wrapper .score {
  color: #394955;
  font-size: 28px;
  display: block;
  width: 200px;
  text-align: center;
  margin-top: 70px;
}

.arc.value {
  -moz-animation: fill 2s;
  -webkit-animation: fill 2s;
  animation: fill 2s;
  -moz-transform: rotate(117deg);
  -ms-transform: rotate(117deg);
  -webkit-transform: rotate(117deg);
  transform: rotate(117deg);
  transition: All 5s ease;
  border-color: #47CF73;
}
.arc.value:after {
  content: '';
  position: absolute;
  left: -50px;
  top: 5px;
}
.arc.value::before {
  background-color: #47CF73;
}

.arc[data-value="100"] {
  -moz-animation: fill 2s;
  -webkit-animation: fill 2s;
  animation: fill 2s;
  -moz-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  -webkit-transform: rotate(180deg);
  transform: rotate(180deg);
  transition: All 5s ease;
  border-color: #47CF73;
}
.arc[data-value="100"]:after {
  content: '';
  position: absolute;
  left: -50px;
  top: 5px;
}
.arc[data-value="100"]::before {
  background-color: #47CF73;
}

.arc.value {
  border-color: #FCD000;
}
.arc.value::before {
  background-color: #FCD000;
}

.legend {
  width: 150px;
  text-align: center;
  width: 100%;
  position: absolute;
  bottom: 0;
  font-size: 16px;
}

@-moz-keyframes fill {
  0% {
    -moz-transform: rotate(0deg);
    transform: rotate(0deg);
    border-color: #FF3C41;
  }
  50% {
    -moz-transform: rotate(180deg);
    transform: rotate(180deg);
    border-color: #47CF73;
  }
}
@-webkit-keyframes fill {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
    border-color: #FF3C41;
  }
  50% {
    -webkit-transform: rotate(180deg);
    transform: rotate(180deg);
    border-color: #47CF73;
  }
}
@keyframes fill {
  0% {
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
    border-color: #FF3C41;
  }
  50% {
    -moz-transform: rotate(180deg);
    -ms-transform: rotate(180deg);
    -webkit-transform: rotate(180deg);
    transform: rotate(180deg);
    border-color: #47CF73;
  }
}
</style>