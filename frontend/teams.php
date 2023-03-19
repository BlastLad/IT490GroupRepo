<!DOCTYPE html>
<html>


<?php require(__DIR__ . "/nav.php"); ?>

<h1>Teams page</h1>

<body>

<?php
  $poke1 = file_get_contents("https://pokeapi.co/api/v2/pokemon/pikachu");
  $poke2 = file_get_contents("https://pokeapi.co/api/v2/pokemon/piplup");
  $poke3 = file_get_contents("https://pokeapi.co/api/v2/pokemon/skitty");
  $poke4 = file_get_contents("https://pokeapi.co/api/v2/pokemon/mew");
  $poke5 = file_get_contents("https://pokeapi.co/api/v2/pokemon/lucario");
  $poke6 = file_get_contents("https://pokeapi.co/api/v2/pokemon/bidoof");
  $pikachu = json_decode($poke1, true);
  $piplup = json_decode($poke2, true);
  $skitty = json_decode($poke3, true);
  $mew = json_decode($poke4, true);
  $lucario = json_decode($poke5, true);
  $bidoof = json_decode($poke6, true);
?>

  <button class="collapsible">Team 1</button>
  <div class="content">
  
    <div class="card-container">
  
  <div class = "row">    
  <div class="column">
    <div class="card">
    <img src=<?php echo '"'.$pikachu['sprites']['front_default'].'"';?> style="width:50%">
    <h2><?php echo ucfirst($pikachu["name"]);?></h2>
    <div class="card-details">
      <p>Hey ! This should print the pokemon's type deficits or whatever</p>
    </div>
    </div>
    </div>
    
    <div class="column">
      <div class="card">
      <img src=<?php echo '"'.$piplup['sprites']['front_default'].'"';?> style="width: 50%">
      <h2><?php echo ucfirst($piplup['name']);?></h2>
      <div class="card-details">
        <p>Hey ! we're still in progress</p>
      </div>
      </div>
      </div>

      <div class="column">
        <div class="card">
        <img src=<?php echo '"'.$skitty['sprites']['front_default'].'"';?> style="width: 50%">
        <h2><?php echo ucfirst($skitty['name']);?></h2>
        <div class="card-details">
          <p>Hey ! we're still in progress</p>
        </div>
        </div>
        </div>
      </div>

      <div class = "row">    
        <div class="column">
          <div class="card">
          <img src=<?php echo '"'.$mew['sprites']['front_default'].'"';?> style="width:50%">
          <h2><?php echo ucfirst($mew['name']);?></h2>
          <div class="card-details">
            <p>Hey ! we're still in progress</p>
          </div>
          </div>
          </div>
          
          <div class="column">
            <div class="card">
            <img src=<?php echo '"'.$lucario['sprites']['front_default'].'"';?> style="width: 50%">
            <h2><?php echo ucfirst($lucario['name']);?></h2>
            <div class="card-details">
              <p>Hey ! we're still in progress</p>
            </div>
            </div>
            </div>
      
            <div class="column">
              <div class="card">
              <img src=<?php echo '"'.$bidoof['sprites']['front_default'].'"';?> style="width: 50%">
              <h2><?php echo ucfirst($bidoof['name']);?></h2>
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