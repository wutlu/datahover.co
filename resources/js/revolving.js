(function() {
  var lastTime = 0;
  var vendors = ['ms', 'moz', 'webkit', 'o'];
  for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x)
  {
    window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
    window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
    || window[vendors[x]+'CancelRequestAnimationFrame'];
  }

  if (!window.requestAnimationFrame)
    window.requestAnimationFrame = function(callback, element) {
      var currTime = new Date().getTime();
      var timeToCall = Math.max(0, 16 - (currTime - lastTime));
      var id = window.setTimeout(
        function()
        {
          callback(currTime + timeToCall);
        },
        timeToCall
      );
      lastTime = currTime + timeToCall;

      return id;
    };

  if (!window.cancelAnimationFrame)
    window.cancelAnimationFrame = function(id) {
      clearTimeout(id);
    };
}());

var canvas = document.querySelector('canvas#revolving');
var ctx = canvas.getContext('2d');

canvas.width = 200;
canvas.height = 200;

var count = 800;

var parent = {
  x: canvas.width / 2,
  y: canvas.height / 2,
  radius: 40,
  mass: 10,

  draw: function()
  {
    ctx.fillStyle = 'white';
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, false);
    ctx.fill();
    ctx.closePath();
  }
}

var Circle = function()
{
  this.angle = Math.random() * 2 * Math.PI;
  this.radius = 1;
  this.x = null;
  this.y = null;
  this.rotation = 0;
  this.rotationSpeed = -0.05 + Math.random() * 0.1;
  this.rotationRadius = 1 + Math.random() * 20;
  this.revolutionSpeed = 0.005;
  this.draw = function()
  {
    ctx.fillStyle = 'rgba(0, 0, 0, .5)';
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, false);
    ctx.fill();
    ctx.closePath();
  };

  this.update = function()
  {
    this.radius = 0.4 + 4 / this.rotationRadius;
    this.draw();
    this.rotation += this.rotationSpeed;
    this.angle -= this.revolutionSpeed;
    this.x = parent.x + parent.radius * Math.cos(this.angle) + Math.cos(this.rotation) * this.rotationRadius;
    this.y = parent.y + parent.radius * Math.sin(this.angle) + Math.sin(this.rotation) * this.rotationRadius;
  }
}

var circles = [];

for(var i = 0; i < count; i++)
{
  circles.push(new Circle());
}

function update()
{
  ctx.fillStyle = '#fff';
  ctx.fillRect(0, 0, canvas.width, canvas.height);
  // parent.draw();

  for(var i = 0; i < circles.length; i++)
  {
    var _this = circles[i];
    _this.update();
  }
}

var loop = function()
{
  update()
  requestAnimationFrame(loop)
}

loop();
