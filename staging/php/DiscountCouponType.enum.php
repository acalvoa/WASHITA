<?php

abstract class DiscountCouponType
{
    const Normal = 0;
    
    // Normal discount, but can be used only once per email
    const OneTimePerEmail = 1;

    // Discount for a new customer invited by an influencer
    const StarterKitByInfluencer = 2;

    // personal discount for user.personal_discount_amount
    // now is available only for influencers
    const UserPersonal = 3;
        
}