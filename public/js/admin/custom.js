/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function()
{
    setInterval('autosave()', 5000);
    
});

function autosave()
{
    var formActionParam = $("form").attr("action");
    console.log("autosaving to " + formActionParam);
}