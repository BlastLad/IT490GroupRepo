<!DOCTYPE html>
<html>


<?php require(__DIR__ . "/nav.php"); ?>

<h1>Teams page</h1>

<body>

  <button class="collapsible">Team 1</button>
  
  <div class="content">
  
    <div class="card-container">
  
  <div class = "row">    
  <div class="column">
    <div class="card">
    <img src="https://openseauserdata.com/files/2e13196558094f62d09598e7025575c0.png" style="width:50%">
    <h2>Poke 1</h2>
    <div class="card-details">
      <p>Hey ! we're still in progress</p>
    </div>
    </div>
    </div>
    
    <div class="column">
      <div class="card">
      <img src="https://www.iliketowastemytime.com/sites/default/files/imagecache/blog_image/space-wallpapers-1920x1200.jpg" style="width: 50%">
      <h2>Poke 2</h2>
      <div class="card-details">
        <p>Hey ! we're still in progress</p>
      </div>
      </div>
      </div>

      <div class="column">
        <div class="card">
        <img src="https://www.iliketowastemytime.com/sites/default/files/imagecache/blog_image/space-wallpapers-1920x1200.jpg" style="width: 50%">
        <h2>Poke 3</h2>
        <div class="card-details">
          <p>Hey ! we're still in progress</p>
        </div>
        </div>
        </div>
      </div>

      <div class = "row">    
        <div class="column">
          <div class="card">
          <img src="https://www.iliketowastemytime.com/sites/default/files/imagecache/blog_image/space-wallpapers-1920x1200.jpg" style="width:50%">
          <h2>Poke 4</h2>
          <div class="card-details">
            <p>Hey ! we're still in progress</p>
          </div>
          </div>
          </div>
          
          <div class="column">
            <div class="card">
            <img src="https://www.iliketowastemytime.com/sites/default/files/imagecache/blog_image/space-wallpapers-1920x1200.jpg" style="width: 50%">
            <h2>Poke 5</h2>
            <div class="card-details">
              <p>Hey ! we're still in progress</p>
            </div>
            </div>
            </div>
      
            <div class="column">
              <div class="card">
              <img src="https://www.iliketowastemytime.com/sites/default/files/imagecache/blog_image/space-wallpapers-1920x1200.jpg" style="width: 50%">
              <h2>Poke 6</h2>
              <div class="card-details">
                <p>Hey ! we're still in progress</p>
              </div>
              </div>
              </div>
            </div>
  </div>
  </div>
    
  <!--
  <div class="content">
    <div class="card-container">
     <div class = "row">
     <div class = "column">
      <div class="card">
        <img src="https://via.placeholder.com/150" alt="Card 1 Image">
        <h3>poke 1 </h3>
        <div class="card-details">
          <p>Hey ! we're still in progress</p>
        </div>
      </div>
    </div>
      
    <div class = "column">
      <div class="card">
        <img src="https://via.placeholder.com/150" alt="Card 2 Image">
        <h3>poke 2</h3>
        <div class="card-details">
          <p>Hey ! we're still in progress</p>
        </div>
    </div> 
    </div>

    <div class = "column">
      <div class="card">
        <img src="https://via.placeholder.com/150" alt="Card 3 Image">
        <h3>poke 3</h3>
        <div class="card-details">
          <p>Hey ! we're still in progress</p>
        </div>
      </div>
      </div>
      
    </div>


      <div class="card">
        <img src="https://via.placeholder.com/150" alt="Card 2 Image">
        <h3>poke 4</h3>
        <div class="card-details">
          <p>Hey ! we're still in progress</p>
        </div>
    </div> 
    <div class="card">
      <img src="https://via.placeholder.com/150" alt="Card 2 Image">
      <h3>poke 5</h3>
      <div class="card-details">
        <p>Hey ! we're still in progress</p>
      </div>
  </div> 

  <div class="card">
    <img src="https://via.placeholder.com/150" alt="Card 2 Image">
    <h3>poke 6</h3>
    <div class="card-details">
      <p>Hey ! we're still in progress</p>
    </div>
</div> 


    </div> 
    </div>
  </div>

    
 -->   
   
  
<script>
const coll = document.querySelector(".collapsible");
const content = document.querySelector(".content");

coll.addEventListener("click", function() {
  this.classList.toggle("active");
  if (content.style.maxHeight){
    content.style.maxHeight = null;
  } else {
    content.style.maxHeight = content.scrollHeight + "px";
  } 
});

const cards = document.querySelectorAll(".card");

cards.forEach(card => {
  card.addEventListener("click", function() {
    this.classList.toggle("active");
    const cardDetails = this.querySelector(".card-details");
    if (cardDetails.style.display === "block") {
      cardDetails.style.display = "none";
    } else {
      cardDetails.style.display = "block";
    }
  });
});

</script>    
</body>
</html>