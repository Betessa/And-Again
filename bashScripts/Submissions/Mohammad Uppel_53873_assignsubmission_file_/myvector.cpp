#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;




MyVector::MyVector()
{
data= nullptr;
n_allocated = 0;
n_items=0;
}


MyVector::~MyVector()
{
    delete[] data;
}


size_t MyVector::size() const
{
    return n_items;
}


size_t MyVector::allocated_length() const
{
return n_allocated;
}


void MyVector::push_back(const Thing &t)
{

    if(n_items==n_allocated){
		reallocate(n_allocated*2);
    }
	data[n_items] = t;
	n_items++;
 }


void MyVector::pop_back()
{
data[n_items-1]=0;
n_items--;
if(n_items*4 <n_allocated){
	reallocate(n_allocated/2);
}
}


Thing &MyVector::front()
{

return data[0];
}


Thing &MyVector::back()
{
    \
return data[n_items-1];
}


Thing *MyVector::begin()
{
return data;
}


Thing *MyVector::end()
{
return &data[n_items];
}


Thing &MyVector::operator[](size_t i)
{
    return *(data+ i);
}


Thing &MyVector::at(size_t i)
{
if(i>=n_items){
        throw "Requested index out of bounds.";
}
else{
return data[i];
}
}


void MyVector::reallocate(size_t new_size)
{
    if(new_size==0){
	new_size=1;
	}
	Thing*temp=new Thing[new_size];
	for(size_t i =0; i<n_items; i++){
		temp[i] = data[i];
	}
	data = temp;
	n_allocated = new_size;
}

