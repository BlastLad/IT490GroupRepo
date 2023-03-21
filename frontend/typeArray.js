const typeArray = new Array();



function GetTypeToInt(type)
{
        if (type == 'normal') {
                return 0;
        }
        else if (type == 'fire') {
                return  1;
        }
        else if (type == 'water') {
                return  2;
        }
        else if (type == 'grass') {
                return  3;
        }
        else if (type == 'electric') {
                return  4;
        }
        else if (type == 'ice') {
                return  5;
        }
        else if (type == 'fighting') {
                return  6;
        }
        else if (type == 'poison') {
                return  7;
        }
        else if (type == 'ground') {
                return  8;
        }
        else if (type == 'flying') {
                return  9;
        }
        else if (type == 'physic') {
                return  10;
        }
        else if (type == 'bug') {
                return  11;
        }
        else if (type == 'rock') {
                return  12;
        }
        else if (type == 'ghost') {
                return  13;
        }
        else if (type == 'dragon') {
                return  14;
        }
        else if (type == 'dark') {
                return  15;
        }
        else if (type == 'steel') {
                return  16;
        }
        else if (type == 'fairy') {
                return  1;
        }
        else return  0;
}

//format is attacker is x defender is y
typeArray[0] = new Array(1,1,1,1,1,1,1,1,1,1,1,1,0.5,0,1,1,0.5,1);//normal
typeArray[1] = new Array(1,0.5,0.5,2,1,2,1,1,1,1,1,2,0.5,1,0.5,1,2,1);//fire
typeArray[2] = new Array(1,2,0.5,0.5,1,1,1,1,2,1,1,1,2,1,0.5,1,1,1);//water
typeArray[3] = new Array(1,0.5,2,0.5,1,1,1,0.5,2,0.5,1,0.5,2,1,0.5,1,0.5,1);//grass
typeArray[4] = new Array(
    1,//normal
    1,//fire
    2,//water
    0.5,//grass
    0.5,//electric
    1,//ice
    1,//fightning
    1,//poison
    0,//ground
    2,//flying
    1,//physic
    1,//bug
    1,//rock
    1,//ghost
    0.5,//dragon
    1,//dark
    1,//steel
    1//fairy
);//electric

typeArray[5] = new Array(
    0.5,//normal
    0.5,//fire
    2,//water
    2,//grass
    1,//electric
    0.5,//ice
    1,//fightning
    1,//poison
    2,//ground
    2,//flying
    1,//physic
    1,//bug
    1,//rock
    1,//ghost
    2,//dragon
    1,//dark
    0.5,//steel
    1//fairy
);//ice

typeArray[6] = new Array(
    2,//normal
    1,//fire
    1,//water
    1,//grass
    1,//electric
    2,//ice
    1,//fightning
    0.5,//poison
    1,//ground
    0.5,//flying
    0.5,//physic
    0.5,//bug
    2,//rock
    0,//ghost
    2,//dragon
    2,//dark
    2,//steel
    0.5//fairy
);//fighting

typeArray[7] = new Array(
    1,//normal
    1,//fire
    1,//water
    2,//grass
    1,//electric
    1,//ice
    1,//fightning
    0.5,//poison
    0.5,//ground
    1,//flying
    1,//physic
    1,//bug
    0.5,//rock
    0.5,//ghost
    1,//dragon
    1,//dark
    0,//steel
    2//fairy
);//poison

typeArray[8] = new Array(
    1,//normal
    2,//fire
    1,//water
    0.5,//grass
    2,//electric
    1,//ice
    1,//fightning
    2,//poison
    0,//ground
    0.5,//flying
    1,//physic
    0.5,//bug
    2,//rock
    1,//ghost
    1,//dragon
    1,//dark
    2,//steel
    1//fairy
);//ground

typeArray[9] = new Array(
    1,//normal
    1,//fire
    1,//water
    2,//grass
    0.5,//electric
    1,//ice
    2,//fightning
    1,//poison
    1,//ground
    1,//flying
    1,//physic
    2,//bug
    0.5,//rock
    1,//ghost
    1,//dragon
    1,//dark
    0.5,//steel
    1//fairy
);//flying

typeArray[10] = new Array(
    1,//normal
    1,//fire
    1,//water
    1,//grass
    1,//electric
    1,//ice
    2,//fightning
    2,//poison
    1,//ground
    1,//flying
    0.5,//physic
    1,//bug
    1,//rock
    1,//ghost
    1,//dragon
    0,//dark
    0.5,//steel
    1//fairy
);//physic

typeArray[11] = new Array(
    1,//normal
    0.5,//fire
    1,//water
    2,//grass
    1,//electric
    1,//ice
    0.5,//fightning
    0.5,//poison
    1,//ground
    0.5,//flying
    2,//physic
    1,//bug
    1,//rock
    0.5,//ghost
    1,//dragon
    2,//dark
    0.5,//steel
    0.5//fairy
);//bug

typeArray[12] = new Array(
    1,//normal
    2,//fire
    1,//water
    1,//grass
    1,//electric
    2,//ice
    0.5,//fightning
    1,//poison
    0.5,//ground
    2,//flying
    1,//physic
    2,//bug
    1,//rock
    1,//ghost
    1,//dragon
    1,//dark
    0.5,//steel
    1//fairy
);//rock

typeArray[13] = new Array(
    0,//normal
    1,//fire
    1,//water
    1,//grass
    1,//electric
    1,//ice
    1,//fightning
    1,//poison
    1,//ground
    1,//flying
    2,//physic
    1,//bug
    1,//rock
    2,//ghost
    1,//dragon
    0.5,//dark
    1,//steel
    1//fairy
);//ghost

typeArray[14] = new Array(
    1,//normal
    1,//fire
    1,//water
    1,//grass
    1,//electric
    1,//ice
    1,//fightning
    1,//poison
    1,//ground
    1,//flying
    1,//physic
    1,//bug
    1,//rock
    1,//ghost
    2,//dragon
    1,//dark
    0.5,//steel
    0//fairy
);//dragon

typeArray[15] = new Array(
    1,//normal
    1,//fire
    1,//water
    1,//grass
    1,//electric
    1,//ice
    0.5,//fightning
    1,//poison
    1,//ground
    1,//flying
    2,//physic
    1,//bug
    1,//rock
    2,//ghost
    1,//dragon
    0.5,//dark
    1,//steel
    0.5//fairy
);//dark

typeArray[16] = new Array(
    1,//normal
    0.5,//fire
    0.5,//water
    1,//grass
    0.5,//electric
    2,//ice
    1,//fightning
    1,//poison
    1,//ground
    1,//flying
    1,//physic
    1,//bug
    2,//rock
    1,//ghost
    1,//dragon
    1,//dark
    0.5,//steel
    2//fairy
);//steel

typeArray[17] = new Array(
    1,//normal
    0.5,//fire
    1,//water
    1,//grass
    1,//electric
    1,//ice
    2,//fightning
    0.5,//poison
    1,//ground
    1,//flying
    1,//physic
    1,//bug
    1,//rock
    1,//ghost
    2,//dragon
    2,//dark
    0.5,//steel
    1//fairy
);//fairy