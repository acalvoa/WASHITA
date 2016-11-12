<?php


function RatingControl($name, $rating){
     return 
     '<div class="star-rating">
                <div class="star-rating__wrap">
                    <input class="star-rating__input" id="star-rating-4-'.$name.'" type="radio" name="'.$name.'" value="4" '.getCheckFor(4, $rating).'>
                    <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-4-'.$name.'" title="4 out of 4 stars"></label>
                    <input class="star-rating__input" id="star-rating-3-'.$name.'" type="radio" name="'.$name.'" value="3" '.getCheckFor(3, $rating).'>
                    <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-3-'.$name.'" title="3 out of 4 stars"></label>
                    <input class="star-rating__input" id="star-rating-2-'.$name.'" type="radio" name="'.$name.'" value="2" '.getCheckFor(2, $rating).'>
                    <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-2-'.$name.'" title="2 out of 4 stars"></label>
                    <input class="star-rating__input" id="star-rating-1-'.$name.'" type="radio" name="'.$name.'" value="1" '.getCheckFor(1, $rating).'>
                    <label class="star-rating__ico fa fa-star-o fa-lg" for="star-rating-1-'.$name.'" title="1 out of 4 stars"></label>
                </div>
        </div>';
   }
   
   function getCheckFor($starValue, $actualRating) {
    return $actualRating == $starValue? 'checked' : '';
} 
