function PFoundationContentClass(id, classuri)
{
    this.id = id;
    this.classuri = classuri;
    
    
    PFoundationContentClass.prototype.cancel()
    {
        console.log("Cancelling: " + this.id);
    }
    
    PFoundationContentClass.prototype.edit()
    {
        console.log("Edit: " + this.id);
    }
    
}